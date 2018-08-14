<?php

namespace App\Http\Controllers;

use App\BatchHistoricalDataAnalysis;
use App\Mail\BatchExpires;
use App\Mail\BatchHistoricalData;
use App\Mail\BatchMappingProduction;
use App\Mail\BatchMappingProvider;
use App\Mail\OrderAwaitingTransmission;
use App\Mail\PurchaseOrderCanceled;
use App\Mail\PurchaseOrderReceived;
use App\Mail\PurchaseOrderTransmission;
use App\Models\BatchExpireDataWork;
use App\Models\BatchForecastRevision;
use App\Models\BatchGenerationForecast;
use App\Models\BatchHistoricalDataWork;
use App\Mail\BatchInventories;
use App\Mail\BatchSalesList;
use App\Models\BatchProcessParameter;
use App\Models\BatchRevisionParameter;
use App\Models\BatchSharingForecast;
use App\Models\Expiry;
use App\Models\ForecastExponentialModel;
use App\Models\ForecastHoltModel;
use App\Models\ForecastWinter2Model;
use App\Models\ForecastWinter4Model;
use App\Models\HistoricalData;
use App\Models\Inventory;
use App\Models\MappingInventoryProduction;
use App\Models\MappingInventoryProvider;
use App\Models\MeanSquareHoltError;
use App\Models\MeanSquareWinter2Error;
use App\Models\MeanSquareWinter4Error;
use App\Models\Production;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderContent;
use App\Models\SalesList;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Float_;
use PhpParser\Node\Stmt\If_;
use function Sodium\increment;
use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF;
use App\Mail\BatchProduction;
use App\Mail\MonitoringExpire;
use App\Mail\SharingForecast;

class BatchController extends Controller
{
    public function verifyToken($token){
        $adminToken = DB::table('users')->where('email','info@smartlogis.it')->select('remember_token')->first();
        if ($adminToken->remember_token==$token){
            $this->CreateInventoryFromFile();
            $this->CreateExpiresFromFile();
            $this->CreateMappingInventoryProviders();
            $this->CreateProductionFromFile();
            $this->CreateMappingInventoryProduction();
            $this->CreateSalesListFromFile();
            $this->HistoricalSeriesGeneration();
            $this->HistoricalSeriesAnalysis();
            $this->GenerationForecast();
            $this->RevisionForecast();
            $this->ProcessParameters();
            $this->RevisionParameters();
            $this->SharingForecast();
            $this->ExpiresMonitorings();
            $this->Monitoring();
            $this->DeleteFiles();
            dd('previsione generata');
        } else return view('errors.500');
    }

    public function DeleteFiles(){
        $file = DB::table('delete_files')->select('*')->get();
        foreach ($file as $objetc){
            $delete = unlink($objetc->filename);
            if ($delete) DB::table('delete_files')->where('id',$objetc->id)->delete();
        }
        return;
    }

    public function Monitoring(){
        $system_day = date('Y-m-d');
        $works = DB::table('batch_monitoring_orders')->join('config_orders','id_config_order','=','configOrder_batchMonOrder')->where('first_day_batch_monitoring_order','<=',$system_day)->where('date_batch_monitoring_order','>=',$system_day)->where('limit_day_batch_monitoring_order','>=',$system_day)->select('*')->get();
        if ($works!=null) {
            foreach ($works as $work) {
                $ordered = false;
                $generated = false;
                //Se specifichiamo il numero di giorni fra un ordine e l'altro e controlliamo il livello di sicurezza
                //Dobbiamo rifornirci solo della quantità necessaria sino al prossimo ordine
                if ($work->execute_config=='1' and $work->level_control=='1'){
                    //Recuperiamo la data dell'ultimo ordine effettuato
                    $last =  DB::table('purchase_orders')->where('company_purchase_order',$work->company_batchMonOrder)->where('state_purchase_order','11')->where('provider_purchase_order',$work->provider_config_order)->orderByDesc('order_date_purchase')->select('order_date_purchase')->first();
                    if ($last!==null){
                        $date_last = date_create(substr($last->order_date_purchase,0,10));
                        $today = date_create(date('Y-m-d'));
                        $days_last_order = date_diff($date_last , $today)->format('%a');
                        $giorni = $work->days_number_config - ($work->days_number_config * 0.1);
                        $giorni = $giorni - $days_last_order;
                        if ($giorni>0) $giorni = $work->days_number_config - $days_last_order; else $giorni = $work->days_number_config;
                    } else $giorni = $work->days_number_config;
                } else $giorni = $work->days_number_config;
                $n=null;
                $year = date('Y');
                while ($n==null){
                    $number = DB::table('purchase_orders')->where('company_purchase_order',$work->company_batchMonOrder)->whereYear('order_date_purchase','=',$year)->orderByDesc('order_number_purchase')->select('order_number_purchase','id_purchase_order')->first();
                    if (count($number)==0) $n=1;
                    else{
                        $id = $number->id_purchase_order;
                        $find = DB::table(('purchase_order_contents'))->where('order_purchase_content',$id)->first();
                        if (count($find)==0){
                            DB::table('purchase_orders')->where('id_purchase_order',$id)->where('company_purchase_order',$work->company_batchMonOrder)->delete();
                        } else $n = $number->order_number_purchase+1;
                    }
                }
                $order = PurchaseOrder::create(
                  [
                      'company_purchase_order' => $work->company_batchMonOrder,
                      'provider_purchase_order' => $work->provider_config_order,
                      'order_date_purchase' => date('Y-m-d'),
                      'order_number_purchase' => $n
                  ]
                );
                $supply = $this->CheckEanMapping($work->company_batchMonOrder,$work->configOrder_batchMonOrder);
                if ($supply==null){
                    $mapping = DB::table('config_orders')->where('id_config_order',$work->configOrder_batchMonOrder)->select('mapping_config')->first();
                    if ($mapping->mapping_config=='11'){
                        $map=$work->company_batchMonOrder;
                        $field='company_mapping_provider';
                    } else {
                        $field='first';
                        if ($mapping->mapping_config=='01') $map='1'; else $map='0';
                    }
                    $item = DB::table('mapping_inventory_providers')->where('company_mapping_provider',$work->company_batchMonOrder)->where($field,$map)->where('provider_mapping_provider',$work->provider_config_order)->select('inventory_mapping_provider as item')->distinct()->get();
                } else {
                    $item = DB::table('sales_lists')->where('company_sales_list',$supply)->join('inventories','ean_inventory','=','ean_saleslist')->where('company_inventory',$work->company_batchMonOrder)->select('id_inventory as item')->distinct()->get();
                }
                if (count($item)>0){
                    $order_total = 0;
                    $order_iva = 0;
                    foreach ($item as $prod){
                        $price=null;
                        $store = DB::table('inventories')->where('id_inventory',$prod->item)->where('company_inventory',$work->company_batchMonOrder)->first();
                        if (count($store)>0){
                            $quantity = $this->CalculateQuantityInOrder($prod->item,$work->company_batchMonOrder,$work->lead_time_config,$giorni);
                            if ($quantity>0){
                                //L'ordine è stato generato!!!
                                $generated = true;

                                //Da sviluppare in futuro a regime la condivisione della disponibilità del fornitore
                                //Nell'eventualità non è disponibile si ricerca un fornitore con un prezzo uguale o minore
                                if ($supply!==null){
                                    $supply_chain = DB::table('supply_chains')->where('company_supply_shares',$supply)->where('company_supply_received',$work->company_batchMonOrder)->select('availability','b2b','ean_mapping')->first();
                                    //-----------------------------

                                    //-----------------------------

                                    //Se le due aziende sono in aggregazione supply chain e condividono i prezzi
                                    if ($supply_chain->b2b=='1'){
                                        if ($supply_chain->ean_mapping=='1'){
                                            $find = DB::table('sales_lists')->where('company_sales_list',$supply)->where('ean_saleslist',$store->ean_inventory)->select('price_b2b','id_sales_list')->first();
                                            $price = $find->price_b2b;
                                        }
                                    }
                                }
                                if (!isset($price)){
                                    $find = DB::table('mapping_inventory_providers')->where('company_mapping_provider',$work->company_batchMonOrder)->where('inventory_mapping_provider',$prod->item)->select('price_provider')->first();
                                    if ($find) $price = $find->price_provider; else $price = 0;
                                }
                                $order_total = $order_total + ($quantity * $price);
                                $order_iva = $order_iva + (($quantity * $price)*$store->imposta_inventory)/100;
                                PurchaseOrderContent::create(
                                  [
                                      'order_purchase_content' => $order->id_purchase_order,
                                      'inventory_purchase_content' => $prod->item,
                                      'quantity_purchase_content' => $quantity,
                                      'unit_price_purchase_content' => $price,
                                      'imposta_purchase_order' => $store->imposta_inventory
                                  ]
                                );
                            }
                        }
                    }
                    if ($generated){
                        $order_iva = round($order_iva,2);
                        $order_total = round($order_total,2);
                        if (($order_total>=$work->min_import_config) and ($work->max_import_config>=$order_total)) {
                            if ($work->transmission_config == 1 or $supply !== null) $state = '10'; else $state = '01';
                        } else $state = '00';
                        if ($state == '00') $messaggio = 'L\'ordine è stato annullato perchè non rispettava i parametri forniti';
                        if ($state == '10') $messaggio = 'L\'ordine è stato trasmesso al fornitore';
                        if ($state == '01') $messaggio = 'L\'ordine è stato generato ed è in attesa di trasmissione';
                        DB::table('purchase_orders')->where('id_purchase_order',$order->id_purchase_order)->update(
                            [
                                'state_purchase_order' => $state,
                                'total_purchase_order' => $order_total,
                                'iva_purchase_order' => $order_iva,
                                'comment_purchase_order' => $messaggio
                            ]
                        );
                        //RECUPERIAMO I DATI PER GENERARE IL PDF DELL'ORDINE
                        if ($supply!==null)
                            $companys = DB::table('supply_chains')->where('company_supply_received',$supply)->where('company_supply_shares',$work->company_batchMonOrder)->join('company_offices','id_company_office','=','company_supply_shares')->join('company_offices as company2','company2.id_company_office','=','company_supply_received')->join('comuni as comune2','comune2.id_comune','=','company2.cap_company')->join('comuni as comune1','comune1.id_comune','=','company_offices.cap_company')->leftjoin('company_offices_extra_italia as extra1','extra1.company_office','=','company_supply_shares')->leftjoin('company_offices_extra_italia as extra2','extra2.company_office','=','company_supply_received')->join('business_profiles','id_admin','=','company_offices.id_admin_company')->select('id_supply_chain','company_supply_shares','company_supply_received','ean_mapping','company_offices.rag_soc_company as rag_soc_shares','company_offices.indirizzo_company as indirizzo_shares','company_offices.civico_company as civico_shares','comune1.cap as cap_shares','comune1.comune as comune_shares','comune1.sigla_prov as sigla_prov_shares','extra1.cap_company_office_extra as cap_extra1','extra1.city_company_office_extra as city_extra1','extra1.state_company_office_extra as state_extra1','company_offices.email_company as email_shares','company2.rag_soc_company as rag_soc_received','company2.indirizzo_company as indirizzo_received','company2.civico_company as civico_received','comune2.cap as cap_received','comune2.comune as comune_received','comune2.sigla_prov as sigla_prov_received','company2.email_company as email_received','extra2.cap_company_office_extra as cap_extra2','extra2.city_company_office_extra as city_extra2','extra2.state_company_office_extra as state_extra2','logo','company_offices.partita_iva_company as partiva','company_offices.telefono_company as telefono','company2.partita_iva_company as iva_provider','company2.telefono_company as telefono_provider')->first();
                        else{
                            $companys = DB::table('company_offices')->where('id_company_office',$work->company_batchMonOrder)->join('comuni','id_comune','=','cap_company')->leftjoin('company_offices_extra_italia as extra','extra.company_office','=','id_company_office')->join('providers','company_provider','=','id_company_office')->where('id_provider',$work->provider_config_order)->join('business_profiles','id_admin','=','id_admin_company')->select('rag_soc_company as rag_soc_shares','rag_soc_provider as rag_soc_received','indirizzo_company as indirizzo_shares','civico_company as civico_shares','address_provider as indirizzo_received','cap_company as cap_shares','cap_company_office_extra as cap_extra1','city_company_office_extra as city_extra1','state_company_office_extra as state_extra1','cap as cap_shares','comune as comune_shares','sigla_prov as sigla_prov_shares','email_company as email_shares','email_provider as email_received','logo','partita_iva_company as partiva','telefono_company as telefono','telefono_provider','iva_provider')->first();
                            $companys->sigla_prov_received = '';
                            $companys->civico_received = '';
                            $companys->cap_received = '';
                            $companys->comune_received = '';
                            $companys->city_received = '';
                            $companys->state_received = '';
                        }
                        $document = DB::table('purchase_orders')->where('id_purchase_order',$order->id_purchase_order)->select('state_purchase_order as state','order_number_purchase as number','order_date_purchase as date','total_purchase_order as total_no_tax','iva_purchase_order as tax')->first();
                        $date = substr($document->date,0,10);
                        $array = explode('-',$date);
                        $document->date = $array[2].'/'.$array[1].'/'.$array[0];
                        if ($supply!==null) $products = DB::table('purchase_order_contents')->where('order_purchase_content',$order->id_purchase_order)->join('inventories','id_inventory','=','inventory_purchase_content')->select('cod_inventory as our_code','ean_inventory as your_code','title_inventory as desc','unit_inventory as unit','quantity_purchase_content as quantity','unit_price_purchase_content as price_unit_no_tax','imposta_purchase_order as tax','discount','expiry_purchase_content as expiry','id_inventory')->get();
                        else $products = DB::table('purchase_order_contents')->where('order_purchase_content',$order->id_purchase_order)->join('inventories','id_inventory','=','inventory_purchase_content')->join('mapping_inventory_providers','inventory_mapping_provider','=','id_inventory')->where('company_mapping_provider',$work->company_batchMonOrder)->where('provider_mapping_provider',$work->provider_config_order)->select('cod_inventory as our_code','cod_mapping_inventory_provider as your_code','title_inventory as desc','unit_inventory as unit','quantity_purchase_content as quantity','unit_price_purchase_content as price_unit_no_tax','imposta_purchase_order as tax','discount','expiry_purchase_content as expiry','id_inventory')->get();
                        //GENERAZIONE DEL PDF DELL'ORDINE
                        $today = date('d/m/Y');
                        $filename = $this->pdforder($products,'PurchaseOrder_','PurchaseOrder',$companys, $document, $today);
                        //TRASMISSIONE AL FORNITORE
                        if ($state == '10' or $supply!==null) {
                                $ordered = true;
                                Mail::to($companys->email_shares)->send(new PurchaseOrderTransmission($filename,$companys->rag_soc_shares,$companys->rag_soc_received,$today));
                                Mail::to($companys->email_received)->send(new PurchaseOrderReceived($filename,$companys->rag_soc_received,$companys->rag_soc_shares,$today));

                                //SE E' IN AGGREGAZIONE SUPPLY CHAIN
                                //A REGIME SI PUO' CREARE UN ORDINE CLIENTE NEL SISTEMA PER L'AZIENDA CHE HA RICEVUTO L'ORDINE E AUMENTARE LA QUANTITA' IMPEGNATA
                                //MOMENTANEAMENTE L'AZIENDA DOVRA' INSERIRLO MANUALMENTE

                                //MODIFICA LE QUANTITA' IN ARRIVO NELL'INVENTARIO PER OGNI PRODOTTO
                                foreach ($products as $item){
                                    $arriving = DB::table('inventories')->where('id_inventory',$item->id_inventory)->select('arriving')->first();
                                    $quant = $arriving->arriving + $item->quantity;
                                    DB::table('inventories')->where('id_inventory',$item->id_inventory)->update(
                                      [
                                          'arriving' => $quant
                                      ]
                                    );
                                }


                        } else {
                                //TRASMISSIONE AL RESPONSABILE ACQUISTI DI UN ORDINE GENERATO MA NON TRASMESSO
                                Mail::to($companys->email_shares)->send(new OrderAwaitingTransmission($filename,$companys->rag_soc_shares,$companys->rag_soc_received,$today));
                        }
                        if ($state='00'){
                            //TRASMETTE UN EMAIL AL RESPONSABILE ACQUISTI DELL'ORDINE ANNULLATO
                            Mail::to($companys->email_shares)->send(new PurchaseOrderCanceled($filename,$companys->rag_soc_shares,$companys->rag_soc_received,$today));
                        }
                        unlink($filename);
                    } else //Cancelliamo l'ordine
                        DB::table('purchase_orders')->where('id_purchase_order',$order->id_purchase_order)->delete();
                }

                //PRENOTAZIONE NUOVA OPERAZIONE DI MONITORAGGIO
                $date_booking = date('Y-m-d');
                $limit_day = date('Y-m-'.$work->window_last_config);
                $first_day = date('Y-m-'.$work->window_first_config);
                if ($ordered){
                    if ($work->level_config=='0'){
                        if ($work->days_number_config>0) {
                            $date_booking = strtotime('+'.$work->days_number_config.' days',strtotime($date_booking));
                            $limit_day = date('Y-m-'.$work->window_last_config,$date_booking);
                            $first_day = date('Y-m-'.$work->window_first_config,$date_booking);
                            $date_booking = date ('Y-m-d', $date_booking);
                        } else {
                            $date_booking = strtotime('+1 month',strtotime($date_booking));
                            $limit_day = date('Y-m-'.$work->window_last_config,$date_booking);
                            $first_day = date('Y-m-'.$work->window_first_config,$date_booking);
                            $date_booking = date ('Y-m-01', $date_booking);
                        }
                    }
                }
                //AGGIORNAMENTO TABELLA DI PRENOTAZIONE
                DB::table('batch_monitoring_orders')->where('id_batch_monitoring_order',$work->id_batch_monitoring_order)->update(
                  [
                      'limit_day_batch_monitoring_order' => $limit_day,
                      'first_day_batch_monitoring_order' => $first_day,
                      'date_batch_monitoring_order' => $date_booking
                  ]
                );
            }
        }
    }

    public function pdforder($product,$view,$controller,$companys, $document, $today){
        $pdf = App::make('dompdf.wrapper')->setPaper('a4');
        $pdf->loadView('pdf.'.$controller, ['items' => $product, 'supply' => $companys, 'document' => $document, 'today' => $today]);
        $time=date('d-m-Y');
        $filename = 'Ordine d\'acquisto N '.$document->number.' del '.$time.' di '. $companys->rag_soc_shares .' a '. $companys->rag_soc_received.'.pdf';
        $pdf->save($filename);
        return $filename;
    }

    public function CalculateQuantityInOrder($item,$company,$leadtime,$giorni){
        //Controlliamo se il prodotto è destinato alla rivendita diretta
        $quantity = 0;
        $sales = DB::table('sales_lists')->where('inventory_sales_list',$item)->where('company_sales_list',$company)->select('id_sales_list','forecast_model','initial_month_sales')->first();
        if (count($sales)>0) $quant_order = $this->CalculateQuantityToOrder($leadtime,$giorni,$sales);
        $quantity = $quantity + $quant_order;
        //Controlliamo se il prodotto partecipa alla produzione per aggiungere altra quantità
        $production = DB::table('mapping_inventory_productions')->where('company_mapping_production',$company)->where('inventory_map_pro',$item)->select('production_map_pro','quantity_mapping_production')->get();
        if (count($production)>0){
            foreach ($production as $prod){
                $sales = DB::table('sales_lists')->where('production_sales_list',$prod->production_map_pro)->where('company_sales_list',$company)->select('id_sales_list','forecast_model','initial_month_sales')->first();
                $quant_order = $this->CalculateQuantityToOrder($leadtime,$giorni,$sales);
                $quantity = $quantity + ($quant_order * $prod->quantity_mapping_production);
            }
        }
        //$quantity rappresenta la quantità che si prevede di vendere sino al prossimo riordino
        $stock = DB::table('inventories')->where('id_inventory',$item)->where('company_inventory',$company)->select('stock','committed','arriving','unit_inventory')->first();
        $quantity_to_order = 0;
        if ($stock){
            if (count($stock)>0) $quantity_stock = $stock->stock - $stock->committed + $stock->arriving;

            else $quantity_stock = 0;

            $quantity_to_order = $quantity - $quantity_stock;
            //Arrotondiamo la quantità se l'unità di misura è NR
            if ($stock->unit_inventory=='NR') $quantity_to_order = ceil($quantity_to_order);
        }
        return $quantity_to_order;
    }

    public function CalculateQuantityToOrder($leadtime,$giorni,$sales){
        $month = floatval(date('m'));
        $index = $this->monthForecast($sales->initial_month_sales,$month);
        if ($sales->forecast_model=='00') $table = $this->tableForecastExponential($sales->id_sales_list);
        if ($sales->forecast_model=='01') $table = $this->tableForecastHolt($sales->id_sales_list);
        if ($sales->forecast_model=='10') $table = $this->tableForecastWinter2($sales->id_sales_list);
        if ($sales->forecast_model=='11') $table = $this->tableForecastWinter4($sales->id_sales_list);
        if ($giorni==0) {
            $day = floatval(date('d'));
            if ($day>30) $day = 30;
            $giorni = 30 - $day;
        }
        $next_order = ($giorni + $leadtime) / 30;
        $explode = explode('.',$next_order);
        $whole_side = floatval($explode[0]);
        if ($whole_side>0) {
            if (isset($explode[1])){
                $decimal_side = '0.'.($explode[1]);
                $decimal_side = floatval($decimal_side);
            } else $decimal_side=0;
        } else $decimal_side = $next_order;
        $quantity = 0;
        if ($whole_side>0){
            for ($i=0;$i<$whole_side;$i++){
                if (($index+$i)<13) $quantity = $quantity + $table[$index+$i];
                else $quantity = $quantity + $table[($index+$i)-12];
            }
        }
        if ($decimal_side>0){
            $index = $index + $whole_side; //Per scorrere la tabella di previsione
            if ($index>=13) $index = $index - 12;
            $quantity = $quantity + ($decimal_side * $table[$index]);
        }
        return $quantity;
    }

    public function tableForecastExponential($id){
        $forecast = DB::table('forecast_exponential_models')->where('ForecastExpoProduct',$id)->select('1','2','3','4','5','6','7','8','9','10','11','12')->first();
        $table = null;
        if (count($forecast)>0){
            $i=1;
            foreach ($forecast as $t) {
                $table[$i] = $t;
                $i++;
            }
        }
        return $table;
    }

    public function tableForecastHolt($id){
        $forecast = DB::table('forecast_holt_models')->where('ForecastHoltProduct',$id)->select('1','2','3','4','5','6','7','8','9','10','11','12')->first();
        $table = null;
        if (count($forecast)>0){
            $i=1;
            foreach ($forecast as $t) {
                $table[$i] = $t;
                $i++;
            }
        }
        return $table;
    }

    public function tableForecastWinter2($id){
        $forecast = DB::table('forecast_winter2_models')->where('Forecastwinter2Product',$id)->select('1','2')->first();
        $table = null;
        if (count($forecast)>0){
            $i=1;
            foreach ($forecast as $t) {
                $table[$i] = $t/6;
                $table[$i+1] = $t/6;
                $table[$i+2] = $t/6;
                $table[$i+3] = $t/6;
                $table[$i+4] = $t/6;
                $table[$i+5] = $t/6;
                $i = 7;
            }
        }
        return $table;
    }

    public function tableForecastWinter4($id){
        $forecast = DB::table('forecast_winter4_models')->where('Forecastwinter4Product',$id)->select('1','2','3','4')->first();
        $table = null;
        if (count($forecast)>0){
            $i=1;
            foreach ($forecast as $t) {
                $table[$i] = $t/3;
                $table[$i+1] = $t/3;
                $table[$i+2] = $t/3;
                $i = $i + 3;
            }
        }
        return $table;
    }



    public function CheckEanMapping($company,$config){
        $find = DB::table('config_orders')->where('id_config_order',$config)->join('providers','id_provider','=','provider_config_order')->where('supply_provider','1')->join('supply_chains','company_supply_shares','=','company_config_order')->where('company_config_order',$company)->select('company_supply_received','ean_mapping')->first();
        if (count($find)>0){
            if ($find->ean_mapping == '1') return $find->company_supply_received; else return null;
        } else return null;
    }

    public function ExpiresMonitorings(){
        $works = DB::table('batch_expiries_monitorings')->where('warned','1')->select('*')->get();
        if ($works!= null){
            foreach ($works as $work){
                $company = DB::table('employees')->where('id_employee',$work->employee_batchExpMon)->join('users','user_employee','=','id')->select('company_employee','name')->first();
                $system = date('Y-m-d');
                $system = strtotime('+'.$work->days_batchExpMon.' days',strtotime($system));
                $system = date ('Y-m-d', $system);
                $find = DB::table('expiries')->where('company_expiry',$company->company_employee)->where('date_expiry','<=',$system)->join('inventories','id_inventory','=','inventory_expiry')->select('inventory_expiry','stock_expiry','date_expiry','cod_inventory','title_inventory','unit_inventory','stock','committed')->orderBy('cod_inventory','date_expiry')->get();
                $filename = $this->pdfview($find,'expire','MonitoringExpire',$company->company_employee);
                Mail::to($work->email_batch_exp_mon)->send(new MonitoringExpire($filename,$company->name));
                $del = unlink($filename);
                if ($del){
                    DB::table('batch_expiries_monitorings')->where('id_batchExpMon',$work->id_batchExpMon)->update(
                        [
                            'warned' => '0'
                        ]
                    );
                }
            } return true;
        } return false;
    }

    public function pdfview($data,$view,$controller,$id_company){
        $rag_soc = DB::table('company_offices')->where('id_company_office',$id_company)->select('rag_soc_company')->first();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.'.$controller, ['items' => $data, 'rag_soc' => $rag_soc->rag_soc_company]);
        $filename = $view .'-'. $rag_soc->rag_soc_company .'.pdf';
        $pdf->save($filename);
        return $filename;
    }

    public function CreateInventoryFromFile(){
        $works = DB::table('batch_inventories')->where('executed_batch_inventory','0')->select('*')->get();
        if ($works!=null){
            foreach ($works as $work){
                $path = 'storage/'.$work->url_file_batch_inventory;
                ini_set('auto_detect_line_endings',TRUE);
                $csv = fopen($path,'r');
                $i=1;
                $problem=0;
                $store=0;
                while ( ($data = fgetcsv($csv) ) !== FALSE ) {
                    if ($i==1){
                        $block=0;
                        if(
                            $data['0']!=='cod_inventory'
                            or $data['1']!='title_inventory'
                            or $data['2']!='category_first'
                            or $data['3']!='category_second'
                            or $data['4']!='unit_inventory'
                            or $data['5']!='stock'
                            or $data['6']!='description_inventory'
                            or $data['7']!='brand'
                            or $data['8']!='ean_inventory'
                            or $data['9']!='average_cost_inventory'
                            or $data['10']!='last_cost_inventory'
                            or $data['11']!='codice_iva_inventory'
                            or $data['12']!='height_inventory'
                            or $data['13']!='width_inventory'
                            or $data['14']!='depth_inventory'
                            or $data['15']!='weight_inventory'
                            or $data['16']!='expire_inventory'
                            or $data['17']!='sale_inventory'
                            or $data['18']!='url_inventory'
                        )   $block=1;
                    }
                    if($i>1){
                        if ($block==0){
                            if (strlen($data['0']>50)) $data['0'] = substr($data['0'],0,50);
                            $block_cod_inventory = $this->FindCodeAndBlock('inventories','company_inventory',$work->company_batch_inventory,'cod_inventory',$data['0']);
                            if ($block_cod_inventory==false){
                                $block_cod_production = $this->FindCodeAndBlock('productions','company_production',$work->company_batch_inventory,'cod_production',$data['0']);
                                if ($block_cod_production==false){
                                //controllo unità di misura
                                $block_unit = $this->FindUnit($data['4']);
                                if ($block_unit==false){
                                    $block_iva = $this->FindIvaCode($data['11']);
                                    if ($block_iva!=null){
                                        if (strlen($data['1']>80)) $data['1'] = substr($data['1'],0,80);
                                        if (strlen($data['2']>50)) $data['2'] = substr($data['2'],0,50);
                                        if (strlen($data['3']>50)) $data['3'] = substr($data['3'],0,50);
                                        if (strlen($data['18']>190)) $data['18'] = '0';
                                        if (strlen($data['7']>30)) $data['7'] = substr($data['7'],0,30);
                                        if (strlen($data['8']>18)) $data['8'] = substr($data['8'],0,18);
                                        $array = explode(",", $data['5']);
                                        $data['5'] = $array[0].'.'.$array[1];
                                        $array = explode(",", $data['12']);
                                        $data['12'] = $array[0].'.'.$array[1];
                                        $array = explode(",", $data['13']);
                                        $data['13'] = $array[0].'.'.$array[1];
                                        $array = explode(",", $data['14']);
                                        $data['14'] = $array[0].'.'.$array[1];
                                        $array = explode(",", $data['15']);
                                        $data['15'] = $array[0].'.'.$array[1];
                                        $array = explode(",", $data['10']);
                                        $data['10'] = $array[0].'.'.$array[1];
                                        $array = explode(",", $data['9']);
                                        $data['9'] = $array[0].'.'.$array[1];
                                        if($data['16']!='0') $data['16']='1';
                                        if($data['17']!='0') $data['17']='1';
                                        $create = Inventory::create(
                                            [
                                                'company_inventory' => $work->company_batch_inventory,
                                                'cod_inventory' => $data['0'],
                                                'title_inventory' => $data['1'],
                                                'category_first' => $data['2'],
                                                'category_second' => $data['3'],
                                                'unit_inventory' => $data['4'],
                                                'stock' => $data['5'],
                                                'url_inventory' => $data['18'],
                                                'description_inventory' => $data['6'],
                                                'brand' => $data['7'],
                                                'ean_inventory' => $data['8'],
                                                'average_cost_inventory' => $data['9'],
                                                'last_cost_inventory' => $data['10'],
                                                'codice_iva_inventory' => $data['11'],
                                                'imposta_inventory' => $block_iva->imposta,
                                                'imposta_desc_inventory' => $block_iva->descrizione,
                                                'height_inventory' => $data['12'],
                                                'width_inventory' => $data['13'],
                                                'depth_inventory' => $data['14'],
                                                'weight_inventory' => $data['15'],
                                                'expire_inventory' => $data['16'],
                                                'sale_inventory' => $data['17'],
                                            ]
                                        );
                                        if ($create) {
                                            DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                            $store=$store+1;
                                            if ($create->sale_inventory=='1'){
                                                $push = SalesList::create([
                                                    'company_sales_list' => $work->company_batch_inventory,
                                                    'inventory_sales_list' => $create->id_inventory,
                                                    'ean_saleslist' => $create->ean_inventory,
                                                ]);
                                                if($push){
                                                    $id=($push->id_sales_list);
                                                    HistoricalData::create(
                                                        [
                                                            'product_historical_data' => $id,
                                                            'company_historical_data' => $push->company_sales_list
                                                        ]
                                                    );
                                                }
                                                if ($work->initial=="historical") {
                                                    //Prenotazione operazione analisi serie storica
                                                    $accessdate=date("Y-m-d");
                                                    BatchHistoricalDataAnalysis::create(
                                                        [
                                                            'CompanyDataAnalysis' => $work->company_batch_inventory,
                                                            'productDataAnalysis' => $id,
                                                            'booking_historical_data_analysi' => $accessdate
                                                        ]
                                                    );
                                                }
                                                if ($work->initial!="new" and $work->initial!="historical"){
                                                    $id_sale = DB::table('sales_lists')->where('inventory_sales_list',$work->initial)->where('company_sales_list',$work->company_batch_inventory)->select('id_sales_list','forecast_model','initial_month_sales')->first();
                                                    if ($id_sale){
                                                        if ($id_sale->forecast_model=='00')
                                                            $work->initial="new";
                                                        else {
                                                            if ($id_sale->forecast_model=='01'){
                                                                $forecast = DB::table('forecast_holt_models')->where('ForecastHoltProduct',$id_sale->id_sales_list)->select('*')->first();
                                                                $i=1;
                                                                $k=1;
                                                                foreach ($forecast as $item){
                                                                    if ($i>7 and $i<20) {
                                                                        $serie[$k] = $item;
                                                                        $k++;
                                                                    }
                                                                    $i++;
                                                                }
                                                                $create = ForecastHoltModel::create(
                                                                    [
                                                                        'ForecastHoltProduct' => $id,
                                                                        'alfa_holt' => $forecast->alfa_holt,
                                                                        'beta_holt' => $forecast->beta_holt,
                                                                        'level_holt' => $forecast->level_holt,
                                                                        'trend_holt' => $forecast->trend_holt,
                                                                        'initial_month_holt' => $forecast->initial_month_holt,
                                                                        '1' => $serie[1],
                                                                        '2' => $serie[2],
                                                                        '3' => $serie[3],
                                                                        '4' => $serie[4],
                                                                        '5' => $serie[5],
                                                                        '6' => $serie[6],
                                                                        '7' => $serie[7],
                                                                        '8' => $serie[8],
                                                                        '9' => $serie[9],
                                                                        '10' => $serie[10],
                                                                        '11' => $serie[11],
                                                                        '12' => $serie[12],
                                                                        'error1' =>  $forecast->error1,
                                                                        'error2' =>  $forecast->error2,
                                                                        'error3' =>  $forecast->error3,
                                                                        'error4' =>  $forecast->error4,
                                                                        'error5' =>  $forecast->error5,
                                                                        'error6' =>  $forecast->error6,
                                                                        'error7' =>  $forecast->error7,
                                                                        'error8' =>  $forecast->error8,
                                                                        'error9' =>  $forecast->error9,
                                                                        'error10' =>  $forecast->error10,
                                                                        'error11' =>  $forecast->error11,
                                                                        'error12' =>  $forecast->error12,
                                                                    ]
                                                                );
                                                                if ($create){
                                                                    $revision = DB::table('mean_square_holt_errors')->where('mean_square_holt',$id_sale->id_sales_list)->get();
                                                                    foreach ($revision as $t){
                                                                        MeanSquareHoltError::create(
                                                                            [
                                                                                'mean_square_holt' => $id,
                                                                                'alfa_mean_square_holt' => $t->alfa_mean_square_holt,
                                                                                'beta_mean_square_holt' => $t->beta_mean_square_holt,
                                                                                'level_mean_square_holt' => $t->level_mean_square_holt,
                                                                                'trend_mean_square_holt' => $t->trend_mean_square_holt,
                                                                                'month_mean_square_holt' => $t->month_mean_square_holt,
                                                                                'mean_square_holt_error' => $t->mean_square_holt_error
                                                                            ]
                                                                        );
                                                                    }
                                                                }
                                                            }
                                                            if ($id_sale->forecast_model=='11'){
                                                                $forecast = DB::table('forecast_winter4_models')->where('Forecastwinter4Product',$id_sale->id_sales_list)->select('*')->first();
                                                                $i=1;
                                                                $k=1;
                                                                foreach ($forecast as $item){
                                                                    if ($i>12 and $i<21) {
                                                                        $serie[$k] = $item;
                                                                        $k++;
                                                                    }
                                                                    $i++;
                                                                }
                                                                $create = ForecastWinter4Model::create(
                                                                    [
                                                                        'Forecastwinter4Product' => $id,
                                                                        'alfa_winter4' => $forecast->alfa_winter4,
                                                                        'beta_winter4' => $forecast->beta_winter4,
                                                                        'gamma_winter4' => $forecast->gamma_winter4,
                                                                        'level_winter4' => $forecast->level_winter4,
                                                                        'trend_winter4' => $forecast->trend_winter4,
                                                                        'initial_month_winter4' => $forecast->initial_month_winter4,
                                                                        'factor1' => $forecast->factor1,
                                                                        'factor2' => $forecast->factor2,
                                                                        'factor3' => $forecast->factor3,
                                                                        'factor4' => $forecast->factor4,
                                                                        '1' => $serie[1],
                                                                        '2' => $serie[2],
                                                                        '3' => $serie[3],
                                                                        '4' => $serie[4],
                                                                        '5' => $serie[5],
                                                                        '6' => $serie[6],
                                                                        '7' => $serie[7],
                                                                        '8' => $serie[8],
                                                                        'error1' =>  $forecast->error1,
                                                                        'error2' =>  $forecast->error2,
                                                                        'error3' =>  $forecast->error3,
                                                                        'error4' =>  $forecast->error4,
                                                                        'error5' =>  $forecast->error5,
                                                                        'error6' =>  $forecast->error6,
                                                                        'error7' =>  $forecast->error7,
                                                                        'error8' =>  $forecast->error8,
                                                                    ]
                                                                );
                                                                if ($create){
                                                                    $revision = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$id_sale->id_sales_list)->get();
                                                                    foreach ($revision as $t){
                                                                        MeanSquareWinter4Error::create(
                                                                            [
                                                                                'mean_square_winter4' => $id,
                                                                                'alfa_mean_square_winter4' => $t->alfa_mean_square_winter4,
                                                                                'beta_mean_square_winter4' => $t->beta_mean_square_winter4,
                                                                                'gamma_mean_square_winter4' => $t->gamma_mean_square_winter4,
                                                                                'level_mean_square_winter4' => $t->level_mean_square_winter4,
                                                                                'trend_mean_square_winter4' => $t->trend_mean_square_winter4,
                                                                                'factor1_mean_square_winter4' => $t->factor1_mean_square_winter4,
                                                                                'factor2_mean_square_winter4' => $t->factor2_mean_square_winter4,
                                                                                'factor3_mean_square_winter4' => $t->factor3_mean_square_winter4,
                                                                                'factor4_mean_square_winter4' => $t->factor4_mean_square_winter4,
                                                                                'month_mean_square_winter4' => $t->month_mean_square_winter4,
                                                                                'mean_square_winter4_error' => $t->mean_square_winter4_error
                                                                            ]
                                                                        );
                                                                    }
                                                                }
                                                            }
                                                            if ($id_sale->forecast_model=='10'){
                                                                $forecast = DB::table('forecast_winter2_models')->where('Forecastwinter2Product',$id_sale->id_sales_list)->select('*')->first();
                                                                $i=1;
                                                                $k=1;
                                                                foreach ($forecast as $item){
                                                                    if ($i>10 and $i<15) {
                                                                        $serie[$k] = $item;
                                                                        $k++;
                                                                    }
                                                                    $i++;
                                                                }
                                                                $create = ForecastWinter2Model::create(
                                                                    [
                                                                        'Forecastwinter2Product' => $id,
                                                                        'alfa_winter2' => $forecast->alfa_winter2,
                                                                        'beta_winter2' => $forecast->beta_winter2,
                                                                        'gamma_winter2' => $forecast->gamma_winter2,
                                                                        'level_winter2' => $forecast->level_winter2,
                                                                        'trend_winter2' => $forecast->trend_winter2,
                                                                        'factor1_winter2' => $forecast->factor1_winter2,
                                                                        'factor2_winter2' => $forecast->factor2_winter2,
                                                                        'initial_month_winter2' => $forecast->initial_month_winter2,
                                                                        '1' => $serie[1],
                                                                        '2' => $serie[2],
                                                                        '3' => $serie[3],
                                                                        '4' => $serie[4],
                                                                        'error1' =>  $forecast->error1,
                                                                        'error2' =>  $forecast->error2,
                                                                        'error3' =>  $forecast->error3,
                                                                        'error4' =>  $forecast->error4,
                                                                    ]
                                                                );
                                                                if ($create){
                                                                    $revision = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$id_sale->id_sales_list)->get();
                                                                    foreach ($revision as $t){
                                                                        MeanSquareWinter2Error::create(
                                                                            [
                                                                                'mean_square_winter2' => $id,
                                                                                'alfa_mean_square_winter2' => $t->alfa_mean_square_winter2,
                                                                                'beta_mean_square_winter2' => $t->beta_mean_square_winter2,
                                                                                'gamma_mean_square_winter2' => $t->gamma_mean_square_winter2,
                                                                                'level_mean_square_winter2' => $t->level_mean_square_winter2,
                                                                                'trend_mean_square_winter2' => $t->trend_mean_square_winter2,
                                                                                'factor1_mean_square_winter2' => $t->factor1_mean_square_winter2,
                                                                                'factor2_mean_square_winter2' => $t->factor2_mean_square_winter2,
                                                                                'month_mean_square_winter2' => $t->month_mean_square_winter2,
                                                                                'mean_square_winter2_error' => $t->mean_square_winter2_error
                                                                            ]
                                                                        );
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        if ($create){
                                                            DB::table('sales_lists')->where('id_sales_list',$id)->update(
                                                                [
                                                                    'forecast_model' => $id_sale->forecast_model,
                                                                    'initial_month_sales' => $id_sale->initial_month_sales,

                                                                ]
                                                            );
                                                            $revision = DB::table('batch_forecast_revisions')->where('forecast_revision',$id_sale->id_sales_list)->where('RevisionForecastModel',$id_sale->forecast_model)->where('executed_revision_forecast','0')->select('period','booking_revision_forecast')->first();
                                                            if ($revision) {
                                                                BatchForecastRevision::create(
                                                                    [
                                                                        'forecast_revision' => $id,
                                                                        'RevisionForecastModel' => $id_sale->forecast_model,
                                                                        'period' => $revision->period,
                                                                        'booking_revision_forecast' => $revision->booking_revision_forecast
                                                                    ]
                                                                );
                                                            }
                                                        }
                                                    } else $work->initial="new";
                                                }
                                                if ($work->initial=="new"){
                                                    $accessdate=date("Y-m-d");
                                                    $date_booking = strtotime('+1 month',strtotime($accessdate));
                                                    $date_booking = date ('Y-m-1', $date_booking);
                                                    //booking operazione di revisione previsione
                                                    BatchForecastRevision::create(
                                                        [
                                                            'forecast_revision' => $id,
                                                            'RevisionForecastModel' => '00',
                                                            'period' => '1',
                                                            'booking_revision_forecast' =>  $date_booking
                                                        ]
                                                    );
                                                }
                                            }
                                        } else $problem++;
                                    } else $problem++;
                                } else $problem++;
                            } else $problem++;
                            } else $problem++;
                        } else $problem++;
                    }
                    $i++;
                }
                fclose($csv);
                ini_set('auto_detect_line_endings',FALSE);
                $array = explode("-", $work->created_at);
                $array[2] = substr($array[2],0,2);
                $data_it = $array[2]."/".$array[1]."/".$array[0];
                $up = DB::table('batch_inventories')->where('id_batch_inventory',$work->id_batch_inventory)->update(
                  [
                      'executed_batch_inventory' => '1'
                  ]
                );
                if ($up) Mail::to($work->email_batch_inventory)->send(new BatchInventories($work->email_batch_inventory,$store,$problem,$data_it));
                unlink($path);
            } return true;
        } else return false;
    }

    public function CreateExpiresFromFile(){
        $works = DB::table('batch_expiries')->where('executed_batch_expiries','0')->select('*')->get();
        if ($works!=null){
            foreach ($works as $work) {
                $path = 'storage/'.$work->url_file_batch_expiries;
                ini_set('auto_detect_line_endings',TRUE);
                $csv = fopen($path,'r');
                $i=1;
                while ( ($data = fgetcsv($csv) ) !== FALSE ) {
                    if ($i==1){
                        $block=0;
                        if (
                            $data['0']!=='cod_inventory'
                            or $data['1']!=='quantity'
                            or $data['2']!=='expire_date'
                        ) $block=1;
                    }
                    if ($i>1){
                        if ($block==0){
                            if (strlen($data['0']>50)) $data['0'] = substr($data['0'],0,50);
                            $block_cod_inventory = $this->FindCodeExpireAndBlock('inventories','company_inventory',$work->company_batch_expiries,'cod_inventory',$data['0']);
                            if ($block_cod_inventory==true){
                                if ($data['1']>0){
                                    $data_expire = explode("/",$data['2']);
                                    $date = $data_expire[2].'-'.$data_expire[1].'-'.$data_expire[0];
                                    if ((preg_match('^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$^',$date))==true)
                                    {
                                        BatchExpireDataWork::create(
                                            [
                                                'id_batchExpDat' => $work->id_batch_expiries,
                                                'date' => $date,
                                                'cod_inventory' => $data['0'],
                                                'quantity' => $data['1']
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $i++;
                }
                fclose($csv);
                ini_set('auto_detect_line_endings',FALSE);
                $item = DB::table('batch_expire_data_works')->where('id_batchExpDat',$work->id_batch_expiries)->select('cod_inventory')->groupBy('cod_inventory')->get();
                $problem=0;
                $create=0;
                foreach ($item as $t){
                    $sum = DB::table('batch_expire_data_works')->where('cod_inventory',$t->cod_inventory)->sum('quantity');
                    $stock = DB::table('inventories')->where('company_inventory',$work->company_batch_expiries)->where('stock',$sum)->where('cod_inventory',$t->cod_inventory)->first();
                    if (count($stock)==1){
                        $create++;
                       DB::table('expiries')->where('company_expiry',$work->company_batch_expiries)->where('inventory_expiry',$stock->id_inventory)->delete();
                       $store = DB::table('batch_expire_data_works')->where('cod_inventory',$t->cod_inventory)->select('date','quantity')->orderBy('date')->get();
                       DB::statement('SET FOREIGN_KEY_CHECKS=0');
                       foreach ($store as $add){
                           Expiry::create(
                               [
                                   'company_expiry' => $work->company_batch_expiries,
                                   'inventory_expiry' => $stock->id_inventory,
                                   'stock_expiry' => $add->quantity,
                                   'date_expiry' => $add->date
                               ]
                           );
                       }
                    } else $problem++;
                }
                DB::table('batch_expire_data_works')->where('id_batchExpDat',$work->id_batch_expiries)->delete();
                $array = explode("-", $work->created_at);
                $array[2] = substr($array[2],0,2);
                $data_it = $array[2]."/".$array[1]."/".$array[0];
                $up = DB::table('batch_expiries')->where('id_batch_expiries',$work->id_batch_expiries)->update(
                    [
                        'executed_batch_expiries' => '1'
                    ]
                );
                if ($up) {
                    unlink($path);
                    Mail::to($work->email_batch_expiries)->send(new BatchExpires($work->email_batch_expiries,$problem,$create,$data_it));
                }
            } return true;
        } else return false;
    }

    public function CreateMappingInventoryProviders(){
        $works = DB::table('batch_mappingInventoryProviders')->where('executed_batch_mapping_provider','0')->select('*')->get();
        if ($works!=null) {
            foreach ($works as $work) {
                $path = 'storage/'.$work->url_file_batch_mapping_provider;
                ini_set('auto_detect_line_endings',TRUE);
                $csv = fopen($path,'r');
                $i=1;
                while ( ($data = fgetcsv($csv) ) !== FALSE ) {
                    if ($i==1){
                        $up=0;
                        $block=0;
                        if (
                            $data['0']!=='cod_inventory'
                            or $data['1']!=='title_inventory'
                            or $data['2']!=='cod_provider'
                            or $data['3']!=='price_provider'
                            or $data['4']!=='first'
                        ) $block=1;
                    }
                    if ($i>1){
                        if ($block==0){
                            if (strlen($data['0']>50)) $data['0'] = substr($data['0'],0,50);
                            $block_cod_inventory = $this->FindCodeAndBlock('inventories','company_inventory',$work->company_batchMapPro,'cod_inventory',$data['0']);
                            if ($block_cod_inventory){
                                if (strlen($data['2']>50)) $data['2'] = substr($data['2'],0,50);
                                $id_inventory = DB::table('inventories')->where('company_inventory',$work->company_batchMapPro)->where('cod_inventory',$data['0'])->select('id_inventory')->first();
                                $exist = DB::table('mapping_inventory_providers')->where('company_mapping_provider',$work->company_batchMapPro)->where('provider_mapping_provider',$work->provider_batchMapPro)->where('inventory_mapping_provider','<>',$id_inventory->id_inventory)->where('cod_mapping_inventory_provider',$data['2'])->get();
                                if (count($exist)==0){
                                    $array = explode(",", $data['3']);
                                    $togliere = explode("€ ",$array[0]);
                                    $price=$togliere[1].'.'.$array[1];
                                    if ($data[4]!=='1' or $data[4]!=='0') $data[4]=1;
                                    $find = DB::table('mapping_inventory_providers')->where('company_mapping_provider',$work->company_batchMapPro)->where('provider_mapping_provider',$work->provider_batchMapPro)->where('inventory_mapping_provider',$id_inventory->id_inventory)->select('*')->first();
                                    if (count($find)>0){
                                        $up++;
                                        DB::table('mapping_inventory_providers')->where('company_mapping_provider',$work->company_batchMapPro)->where('provider_mapping_provider',$work->provider_batchMapPro)->where('inventory_mapping_provider',$id_inventory->id_inventory)->update(
                                            [
                                                'cod_mapping_inventory_provider' => $data['2'],
                                                'price_provider' => $price,
                                                'first' => $data['4']
                                            ]
                                        );
                                    } else {
                                        MappingInventoryProvider::create(
                                            [
                                                 'company_mapping_provider' => $work->company_batchMapPro,
                                                 'inventory_mapping_provider' => $id_inventory->id_inventory,
                                                 'provider_mapping_provider' => $work->provider_batchMapPro,
                                                 'cod_mapping_inventory_provider' => $data['2'],
                                                 'price_provider' => $price,
                                                 'first' => $data['4']
                                            ]
                                        );
                                        $up++;
                                    }
                                }
                            }
                        }
                    }
                    $i++;
                }
                fclose($csv);
                ini_set('auto_detect_line_endings',FALSE);
                $array = explode("-", $work->created_at);
                $array[2] = substr($array[2],0,2);
                $data_it = $array[2]."/".$array[1]."/".$array[0];
                $up = DB::table('batch_mappingInventoryProviders')->where('id_batch_mapping_inventory_provider',$work->id_batch_mapping_inventory_provider)->update(
                    [
                        'executed_batch_mapping_provider' => '1'
                    ]
                );
                if ($up) {
                    $rag = DB::table('providers')->where('company_provider',$work->company_batchMapPro)->where('id_provider',$work->provider_batchMapPro)->select('rag_soc_provider')->first();
                    Mail::to($work->email_batch_mapping_provider)->send(new BatchMappingProvider($work->email_batch_mapping_provider,$rag->rag_soc_provider,$data_it,$up));
                    unlink($path);
                }
            } return true;
        } else return false;
    }

    public function CreateProductionFromFile(){
        $works = DB::table('batch_productions')->where('executed_batch_production','0')->select('*')->get();
        if ($works!=null){
            foreach ($works as $work){
             //   dd($work);
                $path = 'storage/'.$work->url_file_batch_production;
                ini_set('auto_detect_line_endings',TRUE);
                $csv = fopen($path,'r');
                $i=1;
                $problem=0;
                $store=0;
                while ( ($data = fgetcsv($csv) ) !== FALSE ) {
                    if ($i==1){
                        $block=0;
                        if(
                            $data['0']!=='cod_production'
                            or $data['1']!='title_production'
                            or $data['2']!='category_first'
                            or $data['3']!='category_second'
                            or $data['4']!='unit_production'
                            or $data['5']!='description_production'
                            or $data['6']!='brand'
                            or $data['7']!='ean_production'
                            or $data['8']!='codice_iva_production'
                            or $data['9']!='height_production'
                            or $data['10']!='width_production'
                            or $data['11']!='depth_production'
                            or $data['12']!='weight_production'
                            or $data['13']!='url_production'
                        )   $block=1;
                    }
                    if ($i>1){
                        if ($block==0){
                            if (strlen($data['0']>50)) $data['0'] = substr($data['0'],0,50);
                            $block_cod_inventory = $this->FindCodeAndBlock('inventories','company_inventory',$work->company_batch_production,'cod_inventory',$data['0']);
                            if ($block_cod_inventory==false){
                                $block_cod_production = $this->FindCodeAndBlock('productions','company_production',$work->company_batch_production,'cod_production',$data['0']);
                                if ($block_cod_production==false){
                                    //controllo unità di misura
                                    $block_unit = $this->FindUnit($data['4']);
                                    if ($block_unit==false) {
                                        $block_iva = $this->FindIvaCode($data['8']);
                                        if ($block_iva != null) {
                                            if (strlen($data['1']>80)) $data['1'] = substr($data['1'],0,80);
                                            if (strlen($data['2']>50)) $data['2'] = substr($data['2'],0,50);
                                            if (strlen($data['3']>50)) $data['3'] = substr($data['3'],0,50);
                                            if (strlen($data['13']>190)) $data['13'] = '0';
                                            if (strlen($data['7']>30)) $data['7'] = substr($data['7'],0,30);
                                            if (strlen($data['6']>18)) $data['6'] = substr($data['6'],0,18);
                                            $array = explode(",", $data['12']);
                                            $data['12'] = $array[0].'.'.$array[1];
                                            $array = explode(",", $data['11']);
                                            $data['11'] = $array[0].'.'.$array[1];
                                            $array = explode(",", $data['10']);
                                            $data['10'] = $array[0].'.'.$array[1];
                                            $array = explode(",", $data['9']);
                                            $data['9'] = $array[0].'.'.$array[1];
                                            $create = Production::create(
                                              [
                                                  'company_production' => $work->company_batch_production,
                                                  'cod_production' => $data['0'],
                                                  'title_production' => $data['1'],
                                                  'category_first_production' => $data['2'],
                                                  'category_second_production' => $data['3'],
                                                  'unit_production' => $data['4'],
                                                  'url_production' => $data['13'],
                                                  'description_production' => $data['5'],
                                                  'brand_production' => $data['6'],
                                                  'ean_production' => $data['7'],
                                                  'codice_iva_production' => $data['8'],
                                                  'imposta_production' => $block_iva->imposta,
                                                  'imposta_desc_production' => $block_iva->descrizione,
                                                  'height_production' => $data['9'],
                                                  'width_production' => $data['10'],
                                                  'depth_production' => $data['11'],
                                                  'weight_production' => $data['12'],
                                              ]
                                            );
                                            if ($create){
                                                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                                $store=$store+1;
                                                $push = SalesList::create([
                                                    'company_sales_list' => $work->company_batch_production,
                                                    'production_sales_list' => $create->id_production,
                                                    'ean_saleslist' => $create->ean_production,
                                                ]);
                                                if($push){
                                                    $id=($push->id_sales_list);
                                                    HistoricalData::create(
                                                        [
                                                            'product_historical_data' => $id,
                                                            'company_historical_data' => $push->company_sales_list
                                                        ]
                                                    );
                                                    if ($work->initial!=='nothing'){
                                                        if ($work->initial!=="new"){
                                                            $id_sale = DB::table('sales_lists')->where('production_sales_list',$work->initial)->where('company_sales_list',$work->company_batch_production)->select('id_sales_list','forecast_model','initial_month_sales')->first();
                                                            if ($id_sale){
                                                                if ($id_sale->forecast_model=='00')
                                                                    $work->initial="new";
                                                                else {
                                                                    if ($id_sale->forecast_model=='01'){
                                                                        $forecast = DB::table('forecast_holt_models')->where('ForecastHoltProduct',$id_sale->id_sales_list)->select('*')->first();
                                                                        $i=1;
                                                                        $k=1;
                                                                        foreach ($forecast as $item){
                                                                            if ($i>7 and $i<20) {
                                                                                $serie[$k] = $item;
                                                                                $k++;
                                                                            }
                                                                            $i++;
                                                                        }
                                                                        $create = ForecastHoltModel::create(
                                                                            [
                                                                                'ForecastHoltProduct' => $id,
                                                                                'alfa_holt' => $forecast->alfa_holt,
                                                                                'beta_holt' => $forecast->beta_holt,
                                                                                'level_holt' => $forecast->level_holt,
                                                                                'trend_holt' => $forecast->trend_holt,
                                                                                'initial_month_holt' => $forecast->initial_month_holt,
                                                                                '1' => $serie[1],
                                                                                '2' => $serie[2],
                                                                                '3' => $serie[3],
                                                                                '4' => $serie[4],
                                                                                '5' => $serie[5],
                                                                                '6' => $serie[6],
                                                                                '7' => $serie[7],
                                                                                '8' => $serie[8],
                                                                                '9' => $serie[9],
                                                                                '10' => $serie[10],
                                                                                '11' => $serie[11],
                                                                                '12' => $serie[12],
                                                                                'error1' =>  $forecast->error1,
                                                                                'error2' =>  $forecast->error2,
                                                                                'error3' =>  $forecast->error3,
                                                                                'error4' =>  $forecast->error4,
                                                                                'error5' =>  $forecast->error5,
                                                                                'error6' =>  $forecast->error6,
                                                                                'error7' =>  $forecast->error7,
                                                                                'error8' =>  $forecast->error8,
                                                                                'error9' =>  $forecast->error9,
                                                                                'error10' =>  $forecast->error10,
                                                                                'error11' =>  $forecast->error11,
                                                                                'error12' =>  $forecast->error12,
                                                                            ]
                                                                        );
                                                                        if ($create){
                                                                            $revision = DB::table('mean_square_holt_errors')->where('mean_square_holt',$id_sale->id_sales_list)->get();
                                                                            foreach ($revision as $t){
                                                                                MeanSquareHoltError::create(
                                                                                    [
                                                                                        'mean_square_holt' => $id,
                                                                                        'alfa_mean_square_holt' => $t->alfa_mean_square_holt,
                                                                                        'beta_mean_square_holt' => $t->beta_mean_square_holt,
                                                                                        'level_mean_square_holt' => $t->level_mean_square_holt,
                                                                                        'trend_mean_square_holt' => $t->trend_mean_square_holt,
                                                                                        'month_mean_square_holt' => $t->month_mean_square_holt,
                                                                                        'mean_square_holt_error' => $t->mean_square_holt_error
                                                                                    ]
                                                                                );
                                                                            }
                                                                        }
                                                                    }
                                                                    if ($id_sale->forecast_model=='11'){
                                                                        $forecast = DB::table('forecast_winter4_models')->where('Forecastwinter4Product',$id_sale->id_sales_list)->select('*')->first();
                                                                        $i=1;
                                                                        $k=1;
                                                                        foreach ($forecast as $item){
                                                                            if ($i>12 and $i<21) {
                                                                                $serie[$k] = $item;
                                                                                $k++;
                                                                            }
                                                                            $i++;
                                                                        }
                                                                        $create = ForecastWinter4Model::create(
                                                                            [
                                                                                'Forecastwinter4Product' => $id,
                                                                                'alfa_winter4' => $forecast->alfa_winter4,
                                                                                'beta_winter4' => $forecast->beta_winter4,
                                                                                'gamma_winter4' => $forecast->gamma_winter4,
                                                                                'level_winter4' => $forecast->level_winter4,
                                                                                'trend_winter4' => $forecast->trend_winter4,
                                                                                'initial_month_winter4' => $forecast->initial_month_winter4,
                                                                                'factor1' => $forecast->factor1,
                                                                                'factor2' => $forecast->factor2,
                                                                                'factor3' => $forecast->factor3,
                                                                                'factor4' => $forecast->factor4,
                                                                                '1' => $serie[1],
                                                                                '2' => $serie[2],
                                                                                '3' => $serie[3],
                                                                                '4' => $serie[4],
                                                                                '5' => $serie[5],
                                                                                '6' => $serie[6],
                                                                                '7' => $serie[7],
                                                                                '8' => $serie[8],
                                                                                'error1' =>  $forecast->error1,
                                                                                'error2' =>  $forecast->error2,
                                                                                'error3' =>  $forecast->error3,
                                                                                'error4' =>  $forecast->error4,
                                                                                'error5' =>  $forecast->error5,
                                                                                'error6' =>  $forecast->error6,
                                                                                'error7' =>  $forecast->error7,
                                                                                'error8' =>  $forecast->error8,
                                                                            ]
                                                                        );
                                                                        if ($create){
                                                                            $revision = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$id_sale->id_sales_list)->get();
                                                                            foreach ($revision as $t){
                                                                                MeanSquareWinter4Error::create(
                                                                                    [
                                                                                        'mean_square_winter4' => $id,
                                                                                        'alfa_mean_square_winter4' => $t->alfa_mean_square_winter4,
                                                                                        'beta_mean_square_winter4' => $t->beta_mean_square_winter4,
                                                                                        'gamma_mean_square_winter4' => $t->gamma_mean_square_winter4,
                                                                                        'level_mean_square_winter4' => $t->level_mean_square_winter4,
                                                                                        'trend_mean_square_winter4' => $t->trend_mean_square_winter4,
                                                                                        'factor1_mean_square_winter4' => $t->factor1_mean_square_winter4,
                                                                                        'factor2_mean_square_winter4' => $t->factor2_mean_square_winter4,
                                                                                        'factor3_mean_square_winter4' => $t->factor3_mean_square_winter4,
                                                                                        'factor4_mean_square_winter4' => $t->factor4_mean_square_winter4,
                                                                                        'month_mean_square_winter4' => $t->month_mean_square_winter4,
                                                                                        'mean_square_winter4_error' => $t->mean_square_winter4_error
                                                                                    ]
                                                                                );
                                                                            }
                                                                        }
                                                                    }
                                                                    if ($id_sale->forecast_model=='10'){
                                                                        $forecast = DB::table('forecast_winter2_models')->where('Forecastwinter2Product',$id_sale->id_sales_list)->select('*')->first();
                                                                        $i=1;
                                                                        $k=1;
                                                                        foreach ($forecast as $item){
                                                                            if ($i>10 and $i<15) {
                                                                                $serie[$k] = $item;
                                                                                $k++;
                                                                            }
                                                                            $i++;
                                                                        }
                                                                        $create = ForecastWinter2Model::create(
                                                                            [
                                                                                'Forecastwinter2Product' => $id,
                                                                                'alfa_winter2' => $forecast->alfa_winter2,
                                                                                'beta_winter2' => $forecast->beta_winter2,
                                                                                'gamma_winter2' => $forecast->gamma_winter2,
                                                                                'level_winter2' => $forecast->level_winter2,
                                                                                'trend_winter2' => $forecast->trend_winter2,
                                                                                'factor1_winter2' => $forecast->factor1_winter2,
                                                                                'factor2_winter2' => $forecast->factor2_winter2,
                                                                                'initial_month_winter2' => $forecast->initial_month_winter2,
                                                                                '1' => $serie[1],
                                                                                '2' => $serie[2],
                                                                                '3' => $serie[3],
                                                                                '4' => $serie[4],
                                                                                'error1' =>  $forecast->error1,
                                                                                'error2' =>  $forecast->error2,
                                                                                'error3' =>  $forecast->error3,
                                                                                'error4' =>  $forecast->error4,
                                                                            ]
                                                                        );
                                                                        if ($create){
                                                                            $revision = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$id_sale->id_sales_list)->get();
                                                                            foreach ($revision as $t){
                                                                                MeanSquareWinter2Error::create(
                                                                                    [
                                                                                        'mean_square_winter2' => $id,
                                                                                        'alfa_mean_square_winter2' => $t->alfa_mean_square_winter2,
                                                                                        'beta_mean_square_winter2' => $t->beta_mean_square_winter2,
                                                                                        'gamma_mean_square_winter2' => $t->gamma_mean_square_winter2,
                                                                                        'level_mean_square_winter2' => $t->level_mean_square_winter2,
                                                                                        'trend_mean_square_winter2' => $t->trend_mean_square_winter2,
                                                                                        'factor1_mean_square_winter2' => $t->factor1_mean_square_winter2,
                                                                                        'factor2_mean_square_winter2' => $t->factor2_mean_square_winter2,
                                                                                        'month_mean_square_winter2' => $t->month_mean_square_winter2,
                                                                                        'mean_square_winter2_error' => $t->mean_square_winter2_error
                                                                                    ]
                                                                                );
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                if ($create){
                                                                    DB::table('sales_lists')->where('id_sales_list',$id)->update(
                                                                        [
                                                                            'forecast_model' => $id_sale->forecast_model,
                                                                            'initial_month_sales' => $id_sale->initial_month_sales,

                                                                        ]
                                                                    );
                                                                    $revision = DB::table('batch_forecast_revisions')->where('forecast_revision',$id_sale->id_sales_list)->where('RevisionForecastModel',$id_sale->forecast_model)->where('executed_revision_forecast','0')->select('period','booking_revision_forecast')->first();
                                                                    if ($revision) {
                                                                        BatchForecastRevision::create(
                                                                            [
                                                                                'forecast_revision' => $id,
                                                                                'RevisionForecastModel' => $id_sale->forecast_model,
                                                                                'period' => $revision->period,
                                                                                'booking_revision_forecast' => $revision->booking_revision_forecast
                                                                            ]
                                                                        );
                                                                    }
                                                                }
                                                            } else $work->initial="new";
                                                        }
                                                        if ($work->initial=="new"){
                                                            $accessdate=date("Y-m-d");
                                                            $date_booking = strtotime('+1 month',strtotime($accessdate));
                                                            $date_booking = date ('Y-m-1', $date_booking);
                                                            //booking operazione di revisione previsione
                                                            BatchForecastRevision::create(
                                                                [
                                                                    'forecast_revision' => $id,
                                                                    'RevisionForecastModel' => '00',
                                                                    'period' => '1',
                                                                    'booking_revision_forecast' =>  $date_booking
                                                                ]
                                                            );
                                                        }
                                                    }
                                                }
                                            } else $problem++;
                                        } else $problem++;
                                    } else $problem++;
                                } else $problem++;
                            } else $problem++;
                        } else $problem++;
                    } $i++;
                }
                fclose($csv);
                ini_set('auto_detect_line_endings',FALSE);
                $array = explode("-", $work->created_at);
                $array[2] = substr($array[2],0,2);
                $data_it = $array[2]."/".$array[1]."/".$array[0];
                $up = DB::table('batch_productions')->where('id_batch_production',$work->id_batch_production)->update(
                    [
                        'executed_batch_production' => '1'
                    ]
                );
                if ($up) Mail::to($work->email_batch_production)->send(new BatchProduction($work->email_batch_production,$store,$problem,$data_it));
                unlink($path);
            } return true;
        } else return false;
    }

    public function CreateMappingInventoryProduction(){
        $works = DB::table('batch_mapping_productions')->where('executed_batch_map_pro','0')->select('*')->get();
        if ($works!=null) {
            foreach ($works as $work) {
                $path = 'storage/'.$work->url_file_batch_map_pro;
                ini_set('auto_detect_line_endings',TRUE);
                $csv = fopen($path,'r');
                $i=1;
                $problem=0;
                $store=0;
                while ( ($data = fgetcsv($csv) ) !== FALSE ) {
                    if ($i==1){
                        $block=0;
                        if(
                            $data['0']!=='cod_production'
                            or $data['1']!='title_production'
                            or $data['2']!='cod_inventory'
                            or $data['3']!='title_inventory'
                            or $data['4']!='quantity'
                        )   $block=1;
                    }
                    if ($i>1){
                        if ($block==0){
                            if (strlen($data['0']>50)) $data['0'] = substr($data['0'],0,50);
                            $block_cod_production = $this->FindCodeAndBlock('productions','company_production',$work->company_batch_map_pro,'cod_production',$data['0']);
                            if($block_cod_production){
                                $id_production = $this->FindCode('productions','company_production',$work->company_batch_map_pro,'cod_production',$data['0'],'id_production');
                                if (strlen($data['2']>50)) $data['2'] = substr($data['2'],0,50);
                                $block_cod_inventory = $this->FindCodeAndBlock('inventories','company_inventory',$work->company_batch_map_pro,'cod_inventory',$data['2']);
                                if($block_cod_inventory){
                                    $id_inventory = $this->FindCode('inventories','company_inventory',$work->company_batch_map_pro,'cod_inventory',$data['2'],'id_inventory');
                                    $array = explode(",", $data['4']);
                                    if (!isset($array[1])) $array[1]='0';
                                    $data['4'] = $array[0].'.'.$array[1];
                                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                    $create = MappingInventoryProduction::create(
                                      [
                                          'company_mapping_production' => $work->company_batch_map_pro,
                                          'inventory_map_pro' => $id_inventory,
                                          'production_map_pro' => $id_production,
                                          'quantity_mapping_production' => $data['4'],
                                      ]
                                    );
                                    if ($create) $store++; else $problem++;
                                } else $problem++;
                            } else $problem++;
                        } else $problem++;
                    } $i++;
                }
                fclose($csv);
                ini_set('auto_detect_line_endings',FALSE);
                $array = explode("-", $work->created_at);
                $array[2] = substr($array[2],0,2);
                $data_it = $array[2]."/".$array[1]."/".$array[0];
                $up = DB::table('batch_mapping_productions')->where('id_batch_mapping_production',$work->id_batch_mapping_production)->update(
                    [
                        'executed_batch_map_pro' => '1'
                    ]
                );
                if ($up) Mail::to($work->email_batch_map_pro)->send(new BatchMappingProduction($work->email_batch_map_pro,$store,$problem,$data_it));
                unlink($path);
            } return true;
        } else return false;
    }

    public function CreateSalesListFromFile(){
        $works = DB::table('batch_sales_lists')->where('executed_batch_sales_list','0')->select('*')->get();
        if ($works!=null) {
            foreach ($works as $work) {
                $path = 'storage/'.$work->url_file_batch_sales_list;
                ini_set('auto_detect_line_endings',TRUE);
                $csv = fopen($path,'r');
                $i=1;
                $problem=0;
                $store=0;
                while ( ($data = fgetcsv($csv) ) !== FALSE ) {
                    if ($i==1){
                        $block=0;
                        if(
                            $data['0']!=='cod_product'
                            or $data['1']!='title_product'
                            or $data['2']!='price_user'
                            or $data['3']!='price_b2b'
                            or $data['4']!='price_visible'
                            or $data['5']!='quantity_visible'
                        )   $block=1;
                    }
                    if ($i>1){
                        if ($block==0){
                            if (strlen($data['0']>50)) $data['0'] = substr($data['0'],0,50);
                            $block_cod_production = $this->FindCodeAndBlock('productions','company_production',$work->company_batch_sales_list,'cod_production',$data['0']);
                            if($block_cod_production){
                                $id_product = $this->FindCode('productions','company_production',$work->company_batch_sales_list,'cod_production',$data['0'],'id_production');
                                $field = "production_sales_list";
                            } else {
                                $block_cod_inventory = $this->FindCodeAndBlock('inventories','company_inventory',$work->company_batch_sales_list,'cod_inventory',$data['0']);
                                if($block_cod_inventory){
                                    $id_product = $this->FindCode('inventories','company_inventory',$work->company_batch_sales_list,'cod_inventory',$data['0'],'id_inventory');
                                    $field = "inventory_sales_list";
                                }
                            }
                            if (isset($id_product)){
                                if($data['4']!=='1') $data['4']='0';
                                if($data['5']!=='1') $data['5']='0';
                                $array = explode("€ ", $data['2']);
                                $str = $array[1];
                                $array = explode(',',$str);
                                $price_user = $array[0].'.'.$array[1];
                                $array = explode("€ ", $data['3']);
                                $str = $array[1];
                                $array = explode(',',$str);
                                $price_b2b = $array[0].'.'.$array[1];
                                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                $update = DB::table('sales_lists')->where('company_sales_list',$work->company_batch_sales_list)->where($field,$id_product)->update(
                                  [
                                      'visible_sales_list' => $data['4'],
                                      'quantity_sales_list' => $data['5'],
                                      'price_user' => $price_user,
                                      'price_b2b' => $price_b2b,
                                  ]
                                );
                                if ($update) $store++; else $problem++;
                            } else $problem++;
                        } else $problem++;
                    } $i++;
                }
                fclose($csv);
                ini_set('auto_detect_line_endings',FALSE);
                $array = explode("-", $work->created_at);
                $array[2] = substr($array[2],0,2);
                $data_it = $array[2]."/".$array[1]."/".$array[0];
                $up = DB::table('batch_sales_lists')->where('id_batch_sales_list',$work->id_batch_sales_list)->update(
                    [
                        'executed_batch_sales_list' => '1'
                    ]
                );
                if ($up) Mail::to($work->email_batch_sales_list)->send(new BatchSalesList($work->email_batch_sales_list,$store,$problem,$data_it));
               // unlink($path);
                dd('fatto');
            } return true;
        } else return false;
    }

    public function FindCodeAndBlock($table,$id_company,$company,$id_code,$code){
        $find = DB::table($table)->where($id_company,$company)->where($id_code,$code)->first();
        if (count($find)>0) return true; else return false;
    }

    public function FindCode($table,$id_company,$company,$id_code,$code,$id){
        $find = DB::table($table)->where($id_company,$company)->where($id_code,$code)->select($id)->first();
        foreach ($find as $value) $id_find = $value;
        return $id_find;
    }

    public function FindCodeExpireAndBlock($table,$id_company,$company,$id_code,$code){
        $find = DB::table($table)->where($id_company,$company)->where($id_code,$code)->where('expire_inventory','1')->first();
        if (count($find)>0) return true; else return false;
    }

    public function FindUnit($unit){
        if ($unit=='NR' or $unit=='nr' or $unit=='Nr' or $unit=='nR' or $unit=='GR' or $unit=='gr' or $unit=='Gr' or $unit=='gR' or $unit=='ML' or $unit=='ml' or $unit=='Ml' or $unit=='mL')
            return false; else return true;
    }

    public function FindIvaCode($iva){
        $iva_code = DB::table('iva')->where('codice_iva',$iva)->select('imposta','descrizione')->first();
        return $iva_code;
    }

    public $Mp;
    public $Md;

    public function HistoricalSeriesGeneration(){
        $works = DB::table('batch_historical_datas')->where('executed_batchHisDat','0')->select('*')->get();
        if ($works!=null){
            foreach ($works as $work){
                $path = 'storage/'.$work->url_batchHisDat;
                ini_set('auto_detect_line_endings',TRUE);
                $csv = fopen($path,'r');
                $i=1;
                $problem=0;
                $store=0;
                while (($data = fgetcsv($csv)) !== false){
                    if ($i==1){
                      $block=0;
                      if (

                          $data['0']!=='sale_date'
                          or $data['1']!=='document_number'
                          or $data['2']!=='cod_inventory'
                          or $data['3']!=='quantity'
                      ) $block=1;
                    }
                    if ($i>1){
                        if ($block==0){
                            if (strlen($data['2']>50)) $data['2'] = substr($data['2'],0,50);
                            $block_cod_inventory = $this->FindCodeAndBlock('inventories','company_inventory',$work->company_batchHisDat,'cod_inventory',$data['2']);
                            if ($block_cod_inventory==true){
                                $data['3'] = round($data['3'],2);
                                $data_sell = explode("/",$data['0']);
                                $date = $data_sell[2].'-'.$data_sell[1].'-'.$data_sell[0];
                                if ((preg_match('^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$^',$date))==true)
                                {
                                   BatchHistoricalDataWork::create(
                                     [
                                        'id_batchHisDat' => $work->id_batchHisDat,
                                        'sale_date' => $date,
                                        'cod_inventory' => $data['2'],
                                        'quantity' => $data['3']
                                     ]
                                   );
                                } else $problem++;
                            } else $problem++;
                        } else $problem++;
                    }
                    $i++;
                }
                fclose($csv);
                ini_set('auto_detect_line_endings',FALSE);
                $check = DB::table('batch_historical_data_works')->where('id_batchHisDat',$work->id_batchHisDat)->select('sale_date','cod_inventory','quantity')->orderBy('sale_date')->get();
                if (count($check)>0){
                    $this->Mp=0;
                    $this->Md=0;
                    foreach ($check as $sell){
                        if ($this->Md==0) {
                            $initial_month = substr($sell->sale_date,5,2);
                            $initial_year = substr($sell->sale_date,0,4);
                            $this->Mp=$initial_month;
                            $this->Md=1;
                        }
                        $catalog = $this->FindCatalog($work->company_batchHisDat,$sell->cod_inventory);
                        if ($catalog!=='0'){
                            //Creiamo e memorizziamo nella tabella 'sales_lists' e 'historical data' il mese iniziale
                            $find_catalog = $this->TakeIdSalesList($catalog,$work->company_batchHisDat,$sell->cod_inventory);
                            $push = DB::table('sales_lists')->where($catalog,$find_catalog)->where('initial_month_sales','1')->where('company_sales_list',$work->company_batchHisDat)->update(
                                [
                                    'initial_month_sales' => $initial_month,
                                ]
                            );
                            if ($push){
                                $id = DB::table('sales_lists')->where($catalog,$find_catalog)->where('company_sales_list',$work->company_batchHisDat)->select('id_sales_list')->first();
                                if($id){
                                    DB::table('historical_datas')->where('product_historical_data',$id->id_sales_list)->where('company_historical_data',$work->company_batchHisDat)->update(
                                        [
                                            'initial_month' => $initial_month
                                        ]
                                    );
                                }
                            }
                        }
                        $sell_month = substr($sell->sale_date,5,2);
                        $sell_year = substr($sell->sale_date,0,4);
                        if($sell_month>=$initial_month){
                            if ($sell_year==$initial_year){
                                if ($sell_month!==$this->Mp){
                                    $this->Md = $this->Md + ($sell_month - $this->Mp);
                                    $this->Mp = $sell_month;
                                }
                                $this->WriteSeries($work->company_batchHisDat,$sell->cod_inventory,$sell->quantity,$this->Md);
                            } else break;
                        } else {
                            if ($sell_year==$initial_year+1){
                                if ($sell_month!==$this->Mp){
                                    $this->Mp = $sell_month;
                                    $mese = $this->Md+$sell_month;
                                }
                                $this->WriteSeries($work->company_batchHisDat,$sell->cod_inventory,$sell->quantity,$mese);
                            } else break;
                        }
                        if (($sell_year>$initial_year) and ($sell_month>=$initial_month)) break;

                    }
                    DB::table('batch_historical_data_works')->where('id_batchHisDat',$work->id_batchHisDat)->delete();

                }
                $array = explode("-", $work->created_at);
                $array[2] = substr($array[2],0,2);
                $data_it = $array[2]."/".$array[1]."/".$array[0];
                $up = DB::table('batch_historical_datas')->where('id_batchHisDat',$work->id_batchHisDat)->update(
                    [
                        'executed_batchHisDat' => '1'
                    ]
                );
                if ($up) {
                    Mail::to($work->email_batchHisDat)->send(new BatchHistoricalData($work->email_batchHisDat,$store,$problem,$data_it));
                    unlink($path);
                }
            } return true;
        } else return false;
    }

    public function FindCatalog($company,$code){
        $inventory = DB::table('inventories')->where('company_inventory',$company)->where('cod_inventory',$code)->where('sale_inventory','1')->first();
        if (count($inventory)>0){
            return 'inventory_sales_list';
        } else{
            $production = DB::table('productions')->where('company_production',$company)->where('cod_production',$code)->first();
            if (count($production)==0) return '0'; else return 'production_sales_list';
        }
    }

    public function WriteSeries($company,$code,$quantity,$mese){
        $catalog = $this->FindCatalog($company,$code);
        if ($catalog!=='0'){
            $find_catalog = $this->TakeIdSalesList($catalog,$company,$code);
            $id = DB::table('sales_lists')->where('company_sales_list',$company)->where($catalog,$find_catalog)->first();
            $id_sales_list = $id->id_sales_list;
            $value = DB::table('historical_datas')->where('company_historical_data',$company)->where('product_historical_data',$id_sales_list)->select($mese)->first();
            foreach ($value as $val){
                    $add_value=$val+$quantity;
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $add = DB::table('historical_datas')->where('company_historical_data',$company)->where('product_historical_data',$id->id_sales_list)->update(
                    [
                        $mese => $add_value
                    ]);
            if ($add) return true;
        }
        return false;
    }

    public function TakeIdSalesList($catalog,$company,$code){
        if ($catalog=='inventory_sales_list'){
            $id = DB::table('inventories')->where('company_inventory',$company)->where('cod_inventory',$code)->select('id_inventory')->first();
            if ($id) return $id->id_inventory;
        } else {
            $id = DB::table('production')->where('company_production',$company)->where('cod_production',$code)->select('id_production')->first();
            if ($id) return $id->id_production;
        }
        return null;
    }

    public function HistoricalSeriesAnalysis(){
        $system_time = date('Y-m-d');
        $works = DB::table('batch_historical_data_analyses')->where('executed','0')->where('booking_historical_data_analysi','<=',$system_time)->select('*')->get();
        if($works!=null){
            foreach ($works as $work){
                $data = DB::table('historical_datas')->where('company_historical_data',$work->CompanyDataAnalysis)->where('product_historical_data',$work->productDataAnalysis)->get();
                if ($data){
                     foreach ($data as $item){
                         $tot_sell = 0;
                         $model = null;
                         $i=0;
                         while ($i<12){
                             $i++;
                             $add = DB::table('historical_datas')->where('product_historical_data',$item->product_historical_data)->where('company_historical_data',$work->CompanyDataAnalysis)->select($i)->first();
                             foreach ($add as $val){
                                 $tot_sell=$tot_sell+$val;
                             }
                         }
                         if ($tot_sell==0) {
                             $accessdate=date("Y-m-d");
                             $date_booking = strtotime('+1 month',strtotime($accessdate));
                             $date_booking = date ('Y-m-1', $date_booking);
                             //booking operazione di revisione previsione
                             BatchForecastRevision::create(
                                 [
                                     'forecast_revision' => $work->productDataAnalysis,
                                     'RevisionForecastModel' => '00',
                                     'period' => '1',
                                     'booking_revision_forecast' =>  $date_booking
                                 ]
                             );
                         } else {
                             if ($tot_sell>10){
                                 $semiseasonality = $this->CheckSemiAnnualSeasonality($item,$tot_sell);
                                 if ($semiseasonality==0){
                                     $quarterlyseasonality = $this->CheckQuarterlySeasonality($item,$tot_sell);
                                     if ($quarterlyseasonality==0){
                                         $model = '01'; // Modello di Holt
                                         $index = 0;
                                     } else {
                                         $model = '11'; // Modello di Winter Trimestrale
                                         $index = $quarterlyseasonality;
                                     }
                                 } else {
                                     $model='10'; //Modello di Winter Semestrale
                                     $index = $semiseasonality;
                                 }
                             } else {
                                 $model = '01'; // Modello di Holt
                                 $index = 0;
                             }
                             if ($model!==null){
                                 BatchGenerationForecast::create(
                                     [
                                         'GenerationForecast' => $item->id_historical_data,
                                         'GenerationForecastModel' => $model,
                                         'booking_generation_forecast' => $system_time,
                                         'index_forecast' => $index
                                     ]
                                 );
                             }
                         }
                     }
                }
                DB::table('batch_historical_data_analyses')->where('id_batch_historical_data_analysi',$work->id_batch_historical_data_analysi)->update(
                    [
                        'executed' => '1'
                    ]
                );
            } return true;
        } else return false;
    }

    public function CheckSemiAnnualSeasonality($series,$tot){
        $i=0;
        $k=0;
        foreach ($series as $item){
            $i++;
            if (($i>2) and ($i<15)){
                $k++;
                $dati[$k]=$item;
            }
        }
        $i=0;
        while ($i<12){
            $i++;
            $ArrayPosCiclo[$i] = 0;
        }
        $i=0;
        while ($i<6){
            $i++;
            $first = $this->TotalSemester($dati,$i);
            $second = $this->TotalSemester($dati,$i+6);
            $perc_first = ($first / $tot) * 100;
            $perc_second = ($second / $tot) * 100;
            if (($perc_first >= 75) and ($perc_first <=100)){
                if ((($perc_second >= 0) and ($perc_second <= 25))){
                    $ArrayPosCiclo[$i] = $perc_first;
                }
            }
            if (($perc_second >= 75) and ($perc_second<=100)){
                if (($perc_first >= 0) and ($perc_first <= 25)){
                    $ArrayPosCiclo[$i+6] = $perc_second;
                }
            }
        }
        $index=0;
        $value=0;
        for ($i=0;$i<12;$i++){

            if ($ArrayPosCiclo[$i+1]>$value){
                $index=$i+1;
                $value=$ArrayPosCiclo[$i+1];
            }
        }
        return $index;
    }

    public function TotalSemester($series,$i){

        $totale = 0;
        if ($i<8){
            $ciclo = $i+6;
            for ($k=$i;$k<$ciclo;$k++){
                $totale = $totale + $series[$k];
            }
        } else {

            for ($k=$i;$k<13;$k++){
                $totale = $totale + $series[$k];
            }
            $ciclo=$i-7;
            for ($k=0;$k<$ciclo;$k++){
                $g=$k+1;
                $totale = $totale + $series[$g];
            }
        }
        return $totale;
    }

    public function CheckQuarterlySeasonality($series,$tot){
        $i=0;
        $k=0;
        foreach ($series as $item){
            $i++;
            if (($i>2) and ($i<15)){
                $k++;
                $dati[$k]=$item;
            }
        }
        $i=0;
        while ($i<12){
            $i++;
            $ArrayPosCiclo[$i] = 0;
        }
        $i=0;
        while ($i<3){
            $i++;
            $first = $this->TotalQuarter($dati,$i);
            $second = $this->TotalQuarter($dati,$i+3);
            $third = $this->TotalQuarter($dati,$i+6);
            $fourth = $this->TotalQuarter($dati,$i+9);
            $perc_first = ($first / $tot) * 100;
            $perc_second = ($second / $tot) * 100;
            $perc_third = ($third / $tot) * 100;
            $perc_fourth = ($fourth / $tot) * 100;
            if (($perc_first >= 35) and ($perc_first <=65)){
                if (($perc_second >= 0) and ($perc_second <= 20)){
                    if (($perc_third >= 35) and ($perc_third <= 65)){
                        if (($perc_fourth >= 0) and ($perc_fourth <=20)){
                            $ArrayPosCiclo[$i] = $perc_first + $perc_third;
                        }
                    }
                }
            }
            if (($perc_second >= 35) and ($perc_second <=65)){
                if (($perc_first >= 0) and ($perc_first <= 20)){
                    if (($perc_fourth >= 35) and ($perc_fourth <= 65)){
                        if (($perc_third >= 0) and ($perc_third <=20)){
                            $ArrayPosCiclo[$i+3] = $perc_second + $perc_fourth;
                        }
                    }
                }
            }
        }
        $index=0;
        $value=0;
        for ($i=0;$i<12;$i++){

            if ($ArrayPosCiclo[$i+1]>$value){
                $index=$i+1;
                $value=$ArrayPosCiclo[$i+1];
            }
        }
        return $index;
    }

    public function TotalQuarter($series,$i){
        $totale = 0;
        if ($i<11){
            $ciclo = $i+3;
            for ($k=$i;$k<$ciclo;$k++){
                $totale = $totale + $series[$k];
            }
        } else {
            for ($k=$i;$k<13;$k++){
                $totale = $totale + $series[$k];
            }
            $ciclo=$i-10;
            for ($k=0;$k<$ciclo;$k++){
                $g=$k+1;
                $totale = $totale + $series[$g];
            }
        }
        return $totale;
    }

    public function GenerationForecast(){
        $system_time = date('Y-m-d');
        $works = DB::table('batch_generation_forecasts')->where('executed_generation_forecast','0')->where('booking_generation_forecast','<=',$system_time)->select('*')->get();
        if($works!=null){
            foreach ($works as $work) {
                $data = DB::table('historical_datas')->where('id_historical_data',$work->GenerationForecast)->first();
                if ($data) {
                    $i=0;
                    $k=0;
                    foreach ($data as $item) {
                        $i++;
                        if (($i>2) and ($i<15)){
                            $k++;
                            $dati[$k]=$item;
                        }
                    }
                    if($work->GenerationForecastModel=='01'){
                        //Se il modello è di Holt
                        $regression = $this->Regression($dati,12);
                        DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->update(
                            [
                                'forecast_model' => '01'
                            ]
                        );
                        $initial = $data->initial_month;
                        //Inizializziamo la tabella mean_square_holt_errors + forecast_holt_models
                        MeanSquareHoltError::create(
                          [
                              'mean_square_holt' => $data->product_historical_data,
                              'level_mean_square_holt' => $regression['level'],
                              'trend_mean_square_holt' => $regression['trend'],
                              'month_mean_square_holt' => 0
                          ]
                        );
                        $forecast = $this->DevelopsForecast(1,12,$regression['level'],$regression['trend'],null);

                        $unit = DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->leftJoin('inventories','inventory_sales_list','=','id_inventory')->leftJoin('productions','production_sales_list','=','id_production')->select('unit_production','unit_inventory')->first();
                        if ($unit->unit_production=='NR' or $unit->unit_inventory=='NR') $forecast = $this->RoundsUpForecast($forecast);
                        $create = ForecastHoltModel::create(
                          [
                              'ForecastHoltProduct' => $data->product_historical_data,
                              'level_holt' => $regression['level'],
                              'trend_holt' => $regression['trend'],
                              'initial_month_holt' => $initial,
                              '1' => $forecast[1],
                              '2' => $forecast[2],
                              '3' => $forecast[3],
                              '4' => $forecast[4],
                              '5' => $forecast[5],
                              '6' => $forecast[6],
                              '7' => $forecast[7],
                              '8' => $forecast[8],
                              '9' => $forecast[9],
                              '10' => $forecast[10],
                              '11' => $forecast[11],
                              '12' => $forecast[12],
                          ]
                        );
                        if ($create){
                            $accessdate=date("Y-m-d");
                            $date_booking = strtotime('+1 month',strtotime($accessdate));
                            $date_booking = date ('Y-m-1', $date_booking);
                        }
                    }

                    if($work->GenerationForecastModel=='11'){
                        $totali = $this->TotalPeriods($dati,4,$work->index_forecast);
                        //Se il modello è di Winter trimestrale
                        $index=$work->index_forecast;
                        for ($i=1;$i<13;$i++){
                            if ($index<13) $series[$i]=$dati[$index];
                            else $series[$i] =$dati[$index-12];
                            $index++;
                        }
                        $regression = $this->Regression($totali,4);
                        $seasonal_factors = $this->GenerateSeasonalFactors($regression['trend'],$regression['level'],$totali,4);
                        DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->update(
                            [
                                'forecast_model' => '11',
                                'initial_month_sales' => $work->index_forecast
                            ]
                        );
                        $regression = $this->Regression($series,12);
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 9, $seasonal_factors[1],'factor1_mean_square_winter4');
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 10, $seasonal_factors[2],'factor2_mean_square_winter4');
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 11, $seasonal_factors[3],'factor3_mean_square_winter4');
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 12, $seasonal_factors[4],'factor4_mean_square_winter4');

                        $forecast = $this->DevelopsForecast(4,12,$regression['level'],$regression['trend'],$seasonal_factors);
                        $unit = DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->leftJoin('inventories','inventory_sales_list','=','id_inventory')->leftJoin('productions','production_sales_list','=','id_production')->select('unit_production','unit_inventory')->first();
                        if ($unit->unit_production=='NR' or $unit->unit_inventory=='NR') $forecast = $this->RoundsUpForecast($forecast);
                        $initial = DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->select('initial_month_sales')->first();
                        $create = ForecastWinter4Model::create(
                          [
                              'Forecastwinter4Product' => $data->product_historical_data,
                              'level_winter4' => $regression['level'],
                              'trend_winter4' => $regression['trend'],
                              'factor1' => $seasonal_factors[1],
                              'factor2' => $seasonal_factors[2],
                              'factor3' => $seasonal_factors[3],
                              'factor4' => $seasonal_factors[4],
                              'initial_month_winter4' => $initial->initial_month_sales,
                              '1' => $forecast[1]*3,
                              '2' => $forecast[4]*3,
                              '3' => $forecast[7]*3,
                              '4' => $forecast[10]*3,
                              '5' => $forecast[1]*3,
                              '6' => $forecast[4]*3,
                              '7' => $forecast[7]*3,
                              '8' => $forecast[10]*3,
                          ]
                        );
                        if ($create){
                            $accessdate=date("Y-m-d");
                            $initial_date=date("Y-$initial->initial_month_sales-d");
                            $date_booking = strtotime('+3 month',strtotime($initial_date));
                            $accessdate = strtotime($accessdate);
                            while ($accessdate>$date_booking) {
                                $date_booking = strtotime('+1 year',$date_booking);
                            }
                            $date_booking = date ('Y-m-1', $date_booking);
                        }

                    }

                   if($work->GenerationForecastModel=='10'){
                       $totali = $this->TotalPeriods($dati,2,$work->index_forecast);
                       //Se il modello è di Winter semestrale
                       $index=$work->index_forecast;
                       for ($i=1;$i<13;$i++){
                            if ($index<13) $series[$i]=$dati[$index];
                            else $series[$i] =$dati[$index-12];
                            $index++;
                       }
                       $regression = $this->Regression($totali,2);
                       $seasonal_factors = $this->GenerateSeasonalFactors($regression['trend'],$regression['level'],$totali,2);
                       DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->update(
                           [
                               'forecast_model' => '10',
                               'initial_month_sales' => $work->index_forecast
                           ]
                       );
                       $regression = $this->Regression($series,12);
                       $this->CreateMeanSquareWinter2Error($data->product_historical_data, $regression, 5, $seasonal_factors[1],'factor1_mean_square_winter2');
                       $this->CreateMeanSquareWinter2Error($data->product_historical_data, $regression, 6, $seasonal_factors[2],'factor2_mean_square_winter2');
                       $forecast = $this->DevelopsForecast(2,12,$regression['level'],$regression['trend'],$seasonal_factors);
                       $unit = DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->leftJoin('inventories','inventory_sales_list','=','id_inventory')->leftJoin('productions','production_sales_list','=','id_production')->select('unit_production','unit_inventory')->first();
                       if ($unit->unit_production=='NR' or $unit->unit_inventory=='NR') $forecast = $this->RoundsUpForecast($forecast);
                       $initial = DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->select('initial_month_sales')->first();
                       $create = ForecastWinter2Model::create(
                           [
                               'Forecastwinter2Product' => $data->product_historical_data,
                               'level_winter2' => $regression['level'],
                               'trend_winter2' => $regression['trend'],
                               'factor1_winter2' => $seasonal_factors[1],
                               'factor2_winter2' => $seasonal_factors[2],
                               'initial_month_winter2' => $initial->initial_month_sales,
                               '1' => $forecast[1]*6,
                               '2' => $forecast[7]*6,
                               '3' => $forecast[1]*6,
                               '4' => $forecast[7]*6,
                           ]
                       );
                       if ($create){
                           $accessdate=date("Y-m-d");
                           $initial_date=date("Y-$initial->initial_month_sales-d");
                           $date_booking = strtotime('+6 month',strtotime($initial_date));
                           $accessdate = strtotime($accessdate);
                           while ($accessdate>$date_booking) {
                               $date_booking = strtotime('+1 year',$date_booking);
                           }
                           $date_booking = date ('Y-m-1', $date_booking);
                       }
                   }

                    if ($create){
                        //Prenotazione operazione di Revisione previsione
                        BatchForecastRevision::create(
                          [
                              'forecast_revision' => $data->product_historical_data,
                              'RevisionForecastModel' => $work->GenerationForecastModel,
                              'period' => 2,
                              'booking_revision_forecast' => $date_booking
                          ]
                        );

                        //Condivisione previsione con i fornitori
                        $shares = DB::table('supply_chains')->where('company_supply_shares',$data->company_historical_data)->where('forecast','1')->get();
                        if (count($shares)>0){
                            $system_time = date('Y-m-d');
                            foreach ($shares as $create){
                                BatchSharingForecast::create(
                                  [
                                      'sharing_forecast' => $create->id_supply_chain,
                                      'sharing_product' => $data->product_historical_data,
                                      'sharing_forecast_model' => $work->GenerationForecastModel,
                                      'booking_sharing_forecast' => $system_time
                                  ]
                                );
                            }
                        }


                        //Modifica dello stato dell'operazione di generazione previsione in eseguita ed eliminazione dati storici
                        DB::table('batch_generation_forecasts')->where('id_generation_forecast',$work->id_generation_forecast)->update(
                          [
                              'executed_generation_forecast' => '1'

                          ]
                        );
                        DB::table('historical_datas')->where('id_historical_data',$work->GenerationForecast)->update(
                          [
                              '1' => 0,
                              '2' => 0,
                              '3' => 0,
                              '4' => 0,
                              '5' => 0,
                              '6' => 0,
                              '7' => 0,
                              '8' => 0,
                              '9' => 0,
                              '10' => 0,
                              '11' => 0,
                              '11' => 0,
                              '12' => 0,
                          ]
                        );
                    }
                }
            }
        }
    }

    public function TotalPeriods($serie,$n,$initial){
        $k=12/$n;
        $y=1;
        $totali[$y]=0;
        for ($i=0;$i<12;$i++){
            if ($i==$k) {
                $y++;
                $totali[$y]=0;
                $k = $k + (12/$n);
            }
            if ($initial+$i>12){
                    $totali[$y] = $totali[$y] + $serie[($initial+$i) - 12];
            } else {
                    $totali[$y] = $totali[$y] + $serie[$initial+$i];
            }
        }
        return $totali;
    }

    public function Regression($series,$n){
        //Media di n
        $total = 0;
        for ($i=0;$i<$n;$i++) $total = $total + ($i+1);
        $media_n = $total / $n;

        //Media dei periodi
        $total = 0;
        for ($i=0;$i<$n;$i++) $total = $total + $series[$i+1];
        $media_periodi = $total / $n;

        //Calcolo trend
        $total = 0;
        for ($i=0;$i<$n;$i++) $total = $total + (($series[$i+1]) * ($i+1));
        $numeratore = $total - ($n * $media_n * $media_periodi);

        $total = 0;
        for ($i=0;$i<$n;$i++) $total = $total + (($i+1) * ($i+1));
        $denominatore = $total - ($n * ($media_n * $media_n));

        if ($denominatore>0) $trend = $numeratore / $denominatore; else $trend = 0;

        //Calcolo del livello
        $level =  $media_periodi - ($trend * $media_n);

        $regression['trend'] = $trend;
        $regression['level'] = $level;

        return $regression;
    }

    public function GenerateSeasonalFactors($trend,$level,$sell,$n){
        for ($i=0;$i<$n;$i++) {
            if ((($trend*($i+1))+$level)>0)
            $factor [$i+1] = $sell[$i+1] / (($trend)+$level);  // <--- ATTENZIONE HO TOLTO $i L'INDICE DA MOLTIPLICARE AL TREND
            else $factor [$i+1]=0;
        }
        return $factor;
    }


    public function DevelopsForecast($k,$n,$level,$trend,$factors){
        if ($factors!=null){
            if ($k==1) $seasonality=$factors;
            for ($i=1;$i<($n+1);$i++) {
                  if ($k>2){
                    if ($i<4) $seasonality = $factors[1];
                    if ($i>3 and $i<7) $seasonality = $factors[2];
                    if ($i>6 and $i<10) $seasonality = $factors[3];
                    if ($i>9 and $i<13) $seasonality = $factors[4];
                  }
                  if ($k==2) {
                    if ($i<7) $seasonality=$factors[1];
                    else $seasonality=$factors[2];
                  }
                $forecast[$i]=(($trend ) + $level) * ($seasonality); // <--- ATTENZIONE HO TOLTO $i L'INDICE DA MOLTIPLICARE AL TREND
                if ($forecast[$i]<=0) $forecast[$i]=0;
            }
        } else {
            for ($i=1;$i<13;$i++){
                $forecast[$i] = ($trend * $i) + $level;
                if ($forecast[$i]<=0) $forecast[$i] = 0;
            }
        }
        return $forecast;
    }

    public function RoundsUpForecast($forecast){
        for ($i=0;$i<count($forecast);$i++) $forecast[$i+1] = round($forecast[$i+1]);
        return $forecast;
    }

    /**
     * @param $data
     * @param $regression
     * @param $work
     * @param $seasonal_factors
     */
    public function CreateMeanSquareWinter4Error($product, $regression, $index, $seasonal_factors, $factor)
    {
        MeanSquareWinter4Error::create(
            [
                'mean_square_winter4' => $product,
                'level_mean_square_winter4' => $regression['level'],
                'trend_mean_square_winter4' => $regression['trend'],
                'month_mean_square_winter4' => $index,
                $factor => $seasonal_factors
            ]
        );
    }

    public function CreateMeanSquareWinter2Error($product, $regression, $index, $seasonal_factors, $factor)
    {
        MeanSquareWinter2Error::create(
            [
                'mean_square_winter2' => $product,
                'level_mean_square_winter2' => $regression['level'],
                'trend_mean_square_winter2' => $regression['trend'],
                'month_mean_square_winter2' => $index,
                $factor => $seasonal_factors
            ]
        );
    }

    public function RevisionForecast(){
        $system_time = date('Y-m-d');
        $works = DB::table('batch_forecast_revisions')->where('executed_revision_forecast','0')->where('booking_revision_forecast','<=',$system_time)->select('*')->get();
        if($works!=null) {
            foreach ($works as $work) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                if ($work->RevisionForecastModel=='00'){
                    $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select('forecast_model')->first();
                    if ($query->forecast_model!==null){
                        if ($work->period>12) $period = $work->period - 12; else $period = $work->period;
                        $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select($period)->first();
                        if ($query) foreach ($query as $t) $sales = $t;
                            if ($work->period=='1'){
                                //Da sales_lists prendiamo il mese iniziale di vendita e le vendite del primo periodo
                                $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select($work->period)->first();
                                if ($query) foreach ($query as $t) $sales = $t;
                                $initial_month = DB::table('sales_lists')->where('id_sales_list',$work->forecast_revision)->select('initial_month_sales','1')->first();
                                DB::table('historical_datas')->where('product_historical_data',$work->forecast_revision)->update(
                                    [
                                        'initial_month' => $initial_month->initial_month_sales,
                                        '1' => $sales //Memorizziamo il dato storico sulle vendite
                                    ]
                                );
                                //Creazione Tabella previsione su ForecastModelExponential
                                $create = ForecastExponentialModel::create(
                                    [
                                        'ForecastExpoProduct' => $work->forecast_revision,
                                        'initial_month_expo' => $initial_month->initial_month_sales,
                                        '2' => $sales, //Previsione elementare
                                        '1' => $sales,
                                    ]
                                );
                                if ($create){
                                    $date_booking = strtotime('+1 month',strtotime($system_time));
                                    $date_booking = date ('Y-m-1', $date_booking);
                                    //book operazione di revisione previsione
                                    BatchForecastRevision::create(
                                        [
                                            'forecast_revision' => $work->forecast_revision,
                                            'RevisionForecastModel' => '00',
                                            'period' => '2',
                                            'booking_revision_forecast' =>  $date_booking
                                        ]
                                    );
                                }
                            } else {
                                $query = DB::table('forecast_exponential_models')->where('ForecastExpoProduct', $work->forecast_revision)->select($period)->first();
                                if ($query) foreach ($query as $t) $forecast = $t;
                                $error = $forecast - $sales;
                                $alfa = 0.4;
                                $newForecast = $this->CalculateForecastExponential($sales,$forecast,$alfa);
                                $update_error = 'error' . ($period);
                                $query = DB::table('forecast_exponential_models')->where('ForecastExpoProduct', $work->forecast_revision)->select('1','2','3','4','5','6','7','8','9','10','11','12')->first();
                                if ($query){
                                    $i=1;
                                    foreach ($query as $t){
                                        $series[$i] = $t;
                                        $i++;
                                    }
                                }
                                for ($i=$period+1;$i<13;$i++) $series[$i] = $newForecast; //I mesi successivi viene modificata la previsione
                                $unit = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
                                if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $revisione = $this->RoundsUpForecast($series);
                                if ($revisione){

                                    DB::table('forecast_exponential_models')->where('ForecastExpoProduct', $work->forecast_revision)->update(
                                        [
                                            '1' => $revisione[1],
                                            '2' => $revisione[2],
                                            '3' => $revisione[3],
                                            '4' => $revisione[4],
                                            '5' => $revisione[5],
                                            '6' => $revisione[6],
                                            '7' => $revisione[7],
                                            '8' => $revisione[8],
                                            '9' => $revisione[9],
                                            '10' => $revisione[10],
                                            '11' => $revisione[11],
                                            '12' => $revisione[12],
                                            $update_error => $error,
                                        ]
                                    );
                                        //Aggiorniamo i dati storici
                                        if ($work->period>12) $period=$period+6;
                                        DB::table('historical_datas')->where('product_historical_data',$work->forecast_revision)->update(
                                            [
                                                $period => $sales //Memorizziamo il dato storico sulle vendite
                                            ]
                                        );
                                            if ($work->period=='18'){
                                                $company = DB::table('sales_lists')->where('id_sales_list',$work->forecast_revision)->select('company_sales_list')->first();
                                                //Prenotiamo un operazione di Analisi serie storica e assegnamo un modello di previsione
                                                $accessdate=date("Y-m-d");
                                                BatchHistoricalDataAnalysis::create(
                                                    [
                                                        'CompanyDataAnalysis' => $company->company_sales_list,
                                                        'productDataAnalysis' => $work->forecast_revision,
                                                        'booking_historical_data_analysi' => $accessdate
                                                    ]
                                                );
                                            } else {
                                                $date_booking = strtotime('+1 month',strtotime($system_time));
                                                $date_booking = date ('Y-m-1', $date_booking);
                                                //book operazione di revisione previsione
                                                BatchForecastRevision::create(
                                                    [
                                                        'forecast_revision' => $work->forecast_revision,
                                                        'RevisionForecastModel' => '00',
                                                        'period' => $work->period+1,
                                                        'booking_revision_forecast' =>  $date_booking
                                                    ]
                                                );
                                            }
                                            if ($work->period=='12'){
                                                $errors = DB::table('forecast_exponential_models')->where('ForecastExpoProduct', $work->forecast_revision)->select(
                                                    'error7','error8','error9','error10','error11','error12','initial_month_expo'
                                                )->first();
                                                $initial_month = $errors->initial_month_expo;
                                                $initial_month = $initial_month + 6;
                                                if ($initial_month>12) $initial_month = $initial_month - 12;
                                                DB::table('forecast_exponential_models')->where('ForecastExpoProduct', $work->forecast_revision)->update(
                                                    [
                                                        '1' => $revisione[7],
                                                        '2' => $revisione[8],
                                                        '3' => $revisione[9],
                                                        '4' => $revisione[10],
                                                        '5' => $revisione[11],
                                                        '6' => $revisione[12],
                                                        '7' => $revisione[12],
                                                        '8' => $revisione[12],
                                                        '9' => $revisione[12],
                                                        '10' => $revisione[12],
                                                        '11' => $revisione[12],
                                                        '12' => $revisione[12],
                                                        'error1' => $errors->error7,
                                                        'error2' => $errors->error8,
                                                        'error3' => $errors->error9,
                                                        'error4' => $errors->error10,
                                                        'error5' => $errors->error11,
                                                        'error6' => $errors->error12,
                                                        'error7' => null,
                                                        'error8' => null,
                                                        'error9' => null,
                                                        'error10' => null,
                                                        'error11' => null,
                                                        'error12' => null,
                                                        'initial_month_expo' => $initial_month
                                                    ]
                                                );
                                                $historic = DB::table('historical_datas')->where('product_historical_data',$work->forecast_revision)->select(
                                                  '7','8','9','10','11','12')->first();
                                                $i=1;
                                                foreach ( $historic as $t) {
                                                    $series[$i] = $t;
                                                    $i++;
                                                }
                                                DB::table('historical_datas')->where('product_historical_data',$work->forecast_revision)->update(
                                                    [
                                                        '1' => $series[1],
                                                        '2' => $series[2],
                                                        '3' => $series[3],
                                                        '4' => $series[4],
                                                        '5' => $series[5],
                                                        '6' => $series[6],
                                                        '7' => 0,
                                                        '8' => 0,
                                                        '9' => 0,
                                                        '10' => 0,
                                                        '11' => 0,
                                                        '12' => 0,
                                                        'initial_month' => $initial_month
                                                    ]

                                                );
                                                DB::table('sales_lists')->where('id_sales_list',$work->forecast_revision)->update(
                                                    [
                                                        '1' => $series[1],
                                                        '2' => $series[2],
                                                        '3' => $series[3],
                                                        '4' => $series[4],
                                                        '5' => $series[5],
                                                        '6' => $series[6],
                                                        '7' => 0,
                                                        '8' => 0,
                                                        '9' => 0,
                                                        '10' => 0,
                                                        '11' => 0,
                                                        '12' => 0,
                                                        'initial_month_sales' => $initial_month
                                                    ]
                                                );
                                            }
                                }

                            }
                    } else {

                        $date_booking = strtotime('+1 month',strtotime($system_time));
                        $date_booking = date ('Y-m-1', $date_booking);
                        //book operazione di revisione previsione
                        BatchForecastRevision::create(
                            [
                                'forecast_revision' => $work->forecast_revision,
                                'RevisionForecastModel' => '00',
                                'period' => '1',
                                'booking_revision_forecast' =>  $date_booking
                            ]
                        );
                    }
                    //Aggiornamento operazione in eseguita
                    $up = DB::table('batch_forecast_revisions')->where('id_forecast_revision', $work->id_forecast_revision)->update(
                        [
                            'executed_revision_forecast' => '1'
                        ]
                    );
                }
                if ($work->RevisionForecastModel=='01'){
                    $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select($work->period-1)->first();
                    if ($query) foreach ($query as $t) $sales = $t;
                    $query = DB::table('forecast_holt_models')->where('ForecastHoltProduct', $work->forecast_revision)->first();
                    if ($query) {
                        $level = $query->level_holt;
                        $trend = $query->trend_holt;
                        $alfa = $query->alfa_holt;
                        $beta = $query->beta_holt;
                        $id = $query->id_forecast_holt_model;
                    }
                    $query = DB::table('forecast_holt_models')->where('ForecastHoltProduct', $work->forecast_revision)->select($work->period-1)->first();
                    if ($query) foreach ($query as $t) $forecast = $t;
                    $error = $forecast - $sales;
                    $newLevel = $this->CalculateLevel($sales, $level, $trend, $alfa, 1);
                    $newTrend = $this->CalculateTrend($newLevel, $level, $trend, $beta);
                    $update_error = 'error' . ($work->period-1);
                    $revisione = $this->DevelopsForecast($work->period, 12, $newLevel, $newTrend, null);
                    $unit = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
                    if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $revisione = $this->RoundsUpForecast($revisione);
                    if ($revisione) {
                        $update = DB::table('forecast_holt_models')->where('id_forecast_holt_model', $id)->update(
                            [
                                '1' => $revisione[1],
                                '2' => $revisione[2],
                                '3' => $revisione[3],
                                '4' => $revisione[4],
                                '5' => $revisione[5],
                                '6' => $revisione[6],
                                '7' => $revisione[7],
                                '8' => $revisione[8],
                                '9' => $revisione[9],
                                '10' => $revisione[10],
                                '11' => $revisione[11],
                                '12' => $revisione[12],
                                'level_holt' => $newLevel,
                                'trend_holt' => $newTrend,
                                $update_error => $error,
                            ]
                        );
                        if ($update) {
                            //Condivisione previsione con i fornitori
                            $company = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select('company_sales_list')->first();
                            $shares = DB::table('supply_chains')->where('company_supply_shares', $company->company_sales_list)->where('forecast', '1')->get();
                            if (count($shares) > 0) {
                                foreach ($shares as $create) {
                                    BatchSharingForecast::create(
                                        [
                                            'sharing_forecast' => $create->id_supply_chain,
                                            'sharing_product' => $work->forecast_revision,
                                            'sharing_forecast_model' => $work->RevisionForecastModel,
                                            'booking_sharing_forecast' => $system_time
                                        ]
                                    );
                                }
                            }

                            //Aggiornamento operazione in eseguita
                            $up = DB::table('batch_forecast_revisions')->where('id_forecast_revision', $work->id_forecast_revision)->update(
                                [
                                    'executed_revision_forecast' => '1'
                                ]
                            );

                            //Prenotazione operazione di Elabora Parametri
                            if ($up) {
                                BatchProcessParameter::create(
                                    [
                                        'process_parameter' => $work->forecast_revision,
                                        'process_parameter_forecast_model' => $work->RevisionForecastModel,
                                        'sales' => $sales,
                                        'booking_process_parameter' => $system_time,
                                        'period' => $work->period
                                    ]
                                );
                            }
                        }
                    }
                }
                if ($work->RevisionForecastModel=='11') {
                    $query = DB::table('forecast_winter4_models')->where('Forecastwinter4Product', $work->forecast_revision)->first();
                    if ($query) {
                        $level = $query->level_winter4;
                        $trend = $query->trend_winter4;
                        $alfa = $query->alfa_winter4;
                        $beta = $query->beta_winter4;
                        $gamma = $query->gamma_winter4;
                        $id = $query->id_forecast_winter4_model;
                        $initial = $query->initial_month_winter4;
                    }
                    if ($work->period>2){
                        if ($work->period==3) $mounth1 = 4;
                        if ($work->period==4) $mounth1 = 7;
                        if ($work->period==5) $mounth1 = 10;
                        if ($work->period==6) $mounth1 = 1;
                        if ($work->period==7) $mounth1 = 4;
                        if ($work->period==8) $mounth1 = 7;
                        if ($work->period==9) $mounth1 = 10;
                    } else $mounth1 = 1;
                    $mounth2 = $mounth1 + 1;
                    $mounth3 = $mounth2 + 1;
                    $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select($mounth1,$mounth2,$mounth3)->first();
                    if ($query){
                        $sales = 0;
                        foreach ($query as $t) $sales = $sales + $t;
                    }
                    $query = DB::table('forecast_winter4_models')->where('Forecastwinter4Product', $work->forecast_revision)->select($work->period-1)->first();
                    if ($query) foreach ($query as $t) $forecast = $t;
                    $error = $forecast - $sales;
                    if ($work->period>5){
                        $period = $work->period-5;
                        $factor = 'factor'.($work->period-5);
                    } else {
                        $period = $work->period - 1;
                        $factor = 'factor'.($work->period-1);
                    }
                    $query = DB::table('forecast_winter4_models')->where('Forecastwinter4Product', $work->forecast_revision)->select($factor)->first();
                    if ($query) foreach ($query as $t) $factor = $t;
                    $newLevel = $this->CalculateLevel($sales/3, $level, $trend, $alfa, $factor);
                    $newTrend = $this->CalculateTrend($newLevel, $level, $trend, $beta);
                    $newfactor = $this->CalculateFactor($sales/3,$newLevel,$gamma,$factor);
                    for ($i=1;$i<5;$i++){
                        if ($i==$period) $factors[$i] = $newfactor;
                        else {
                            $val = 'factor'.$i;
                            $valori[] = $val;
                        }
                    }
                    $query = DB::table('forecast_winter4_models')->where('Forecastwinter4Product', $work->forecast_revision)->select($valori[0],$valori[1],$valori[2])->first();
                    $i=0;
                    foreach ($query as $t){
                        $valori_fact[$i] = $t;
                        $i++;
                    }
                    $k=0;
                    for ($i=1;$i<5;$i++){
                        if ($i==$period) {
                            $factors[$i] = $newfactor;
                        }
                        else {
                            $factors[$i] = $valori_fact[$k];
                            $k++;
                        }
                    }
                    $update_error = 'error' . ($work->period-1);
                    $revisione = $this->DevelopsForecast(4, 12, $newLevel, $newTrend, $factors);
                    $unit = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
                    if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $revisione = $this->RoundsUpForecast($revisione);
                    if ($revisione) {
                        $up_factor ='factor'.$period;
                        $update = DB::table('forecast_winter4_models')->where('id_forecast_winter4_model', $id)->update(
                            [
                                '1' => $revisione[1]*3,
                                '2' => $revisione[4]*3,
                                '3' => $revisione[7]*3,
                                '4' => $revisione[10]*3,
                                '5' => $revisione[1]*3,
                                '6' => $revisione[4]*3,
                                '7' => $revisione[7]*3,
                                '8' => $revisione[10]*3,
                                'level_winter4' => $newLevel,
                                'trend_winter4' => $newTrend,
                                $up_factor => $newfactor,
                                $update_error => $error,
                            ]
                        );
                        if ($update) {
                            //Condivisione previsione con i fornitori
                            $company = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select('company_sales_list')->first();
                            $shares = DB::table('supply_chains')->where('company_supply_shares', $company->company_sales_list)->where('forecast', '1')->get();
                            if (count($shares) > 0) {
                                foreach ($shares as $create) {
                                    BatchSharingForecast::create(
                                        [
                                            'sharing_forecast' => $create->id_supply_chain,
                                            'sharing_product' => $work->forecast_revision,
                                            'sharing_forecast_model' => $work->RevisionForecastModel,
                                            'booking_sharing_forecast' => $system_time
                                        ]
                                    );
                                }
                            }

                            //Aggiornamento operazione in eseguita
                            $up = DB::table('batch_forecast_revisions')->where('id_forecast_revision', $work->id_forecast_revision)->update(
                                [
                                    'executed_revision_forecast' => '1'
                                ]
                            );

                            //Prenotazione operazione di Elabora Parametri
                            if ($up) {
                                BatchProcessParameter::create(
                                    [
                                        'process_parameter' => $work->forecast_revision,
                                        'process_parameter_forecast_model' => $work->RevisionForecastModel,
                                        'sales' => $sales,
                                        'booking_process_parameter' => $system_time,
                                        'period' => $work->period
                                    ]
                                );
                            }
                        }
                    }
                }
                if ($work->RevisionForecastModel=='10') {
                    $query = DB::table('forecast_winter2_models')->where('Forecastwinter2Product', $work->forecast_revision)->first();
                    if ($query) {
                        $level = $query->level_winter2;
                        $trend = $query->trend_winter2;
                        $alfa = $query->alfa_winter2;
                        $beta = $query->beta_winter2;
                        $gamma = $query->gamma_winter2;
                        $id = $query->id_forecast_winter2_model;
                        $initial = $query->initial_month_winter2;
                    }
                    if ($work->period==3 or $work->period==5)$mounth1 = 7;
                    else $mounth1 = 1;
                    $mounth2 = $mounth1 + 1;
                    $mounth3 = $mounth2 + 1;
                    $mounth4 = $mounth3 + 1;
                    $mounth5 = $mounth4 + 1;
                    $mounth6 = $mounth5 + 1;
                    $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select($mounth1,$mounth2,$mounth3,$mounth4,$mounth5,$mounth6)->first();
                    if ($query){
                        $sales = 0;
                        foreach ($query as $t) $sales = $sales + $t;
                    }
                    $query = DB::table('forecast_winter2_models')->where('Forecastwinter2Product', $work->forecast_revision)->select($work->period-1)->first();
                    if ($query) foreach ($query as $t) $forecast = $t;
                    $error = $forecast - $sales;
                    if ($work->period==3 or $work->period==5) {
                        $period = 2;
                        $factor = 'factor2_winter2';
                    }
                    else {
                        $period = 1;
                        $factor = 'factor1_winter2';
                    }
                    $query = DB::table('forecast_winter2_models')->where('Forecastwinter2Product', $work->forecast_revision)->select($factor)->first();
                    if ($query) foreach ($query as $t) $factor = $t;
                    $newLevel = $this->CalculateLevel($sales/6, $level, $trend, $alfa, $factor);
                    $newTrend = $this->CalculateTrend($newLevel, $level, $trend, $beta);
                    $newfactor = $this->CalculateFactor($sales/6,$newLevel,$gamma,$factor);
                    for ($i=1;$i<3;$i++){
                        if ($i==$period) $factors[$i] = $newfactor;
                        else {
                            $val = 'factor'.$i.'_winter2';
                            $valori[] = $val;
                        }
                    }
                    $query = DB::table('forecast_winter2_models')->where('Forecastwinter2Product', $work->forecast_revision)->select($valori[0])->first();
                    $i=0;
                    foreach ($query as $t){
                        $valori_fact[$i] = $t;
                        $i++;
                    }
                    $k=0;
                    for ($i=1;$i<3;$i++){
                        if ($i==$period) {
                            $factors[$i] = $newfactor;
                        }
                        else {
                            $factors[$i] = $valori_fact[$k];
                            $k++;
                        }
                    }
                    $update_error = 'error' . ($work->period-1);
                    $revisione = $this->DevelopsForecast(2, 12, $newLevel, $newTrend, $factors);
                    $unit = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
                    if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $revisione = $this->RoundsUpForecast($revisione);
                    if ($revisione) {
                        $up_factor ='factor'.$period.'_winter2';
                        $update = DB::table('forecast_winter2_models')->where('id_forecast_winter2_model', $id)->update(
                            [
                                '1' => $revisione[1]*6,
                                '2' => $revisione[7]*6,
                                '3' => $revisione[1]*6,
                                '4' => $revisione[7]*6,
                                'level_winter2' => $newLevel,
                                'trend_winter2' => $newTrend,
                                $up_factor => $newfactor,
                                $update_error => $error,
                            ]
                        );
                        if ($update) {
                            //Condivisione previsione con i fornitori
                            $company = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select('company_sales_list')->first();
                            $shares = DB::table('supply_chains')->where('company_supply_shares', $company->company_sales_list)->where('forecast', '1')->get();
                            if (count($shares) > 0) {
                                foreach ($shares as $create) {
                                    BatchSharingForecast::create(
                                        [
                                            'sharing_forecast' => $create->id_supply_chain,
                                            'sharing_product' => $work->forecast_revision,
                                            'sharing_forecast_model' => $work->RevisionForecastModel,
                                            'booking_sharing_forecast' => $system_time
                                        ]
                                    );
                                }
                            }

                            //Aggiornamento operazione in eseguita
                            $up = DB::table('batch_forecast_revisions')->where('id_forecast_revision', $work->id_forecast_revision)->update(
                                [
                                    'executed_revision_forecast' => '1'
                                ]
                            );

                            //Prenotazione operazione di Elabora Parametri
                            if ($up) {
                                BatchProcessParameter::create(
                                    [
                                        'process_parameter' => $work->forecast_revision,
                                        'process_parameter_forecast_model' => $work->RevisionForecastModel,
                                        'sales' => $sales,
                                        'booking_process_parameter' => $system_time,
                                        'period' => $work->period
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function CalculateForecastExponential($sales,$forecast,$alfa){
        $newForecast = ($alfa * $sales) + ((1 - $alfa) * $forecast);
        return $newForecast;
    }

    public function CalculateLevel($sales,$level,$trend,$alfa,$factor){
        if ($factor>0) $level = ($alfa * ($sales / $factor)) + ((1 - $alfa) * ($level + $trend));
        else $level = ((1 - $alfa) * ($level + $trend));
        return $level;
    }

    public function CalculateTrend($level,$oldLevel,$trend,$beta){
        $trend = ($beta * ($level - $oldLevel)) + ((1 - $beta) * $trend);
        return $trend;
    }

    public function CalculateFactor($sales,$newLevel,$gamma,$factor){
        if ($newLevel>0) $factor = ((($sales/$newLevel) * $gamma) + ((1 - $gamma) * $factor));
        else $factor = ((1 - $gamma) * $factor);
        return $factor;

    }

    public function ProcessParameters(){
        $system_time = date('Y-m-d');
        $works = DB::table('batch_process_parameters')->where('executed_process_parameter','0')->where('booking_process_parameter','<=',$system_time)->select('*')->get();
        if($works!=null) {
            foreach ($works as $work) {
                if ($work->process_parameter_forecast_model=="01") $process = $this->ProcessParametersHolt($work->sales,$work->period-1,$work->process_parameter);
                if ($work->process_parameter_forecast_model=="10") $process = $this->ProcessParametersWinter2($work->sales,$work->period-1,$work->process_parameter);
                if ($work->process_parameter_forecast_model=="11") $process = $this->ProcessParametersWinter4($work->sales,$work->period-1,$work->process_parameter);
                if ($process){
                    DB::table('batch_process_parameters')->where('id_process_parameter',$work->id_process_parameter)->update(
                      [
                          'executed_process_parameter' => '1'
                      ]
                    );
                }
            } return true;
        } else return false;
    }

    public function ProcessParametersHolt($sales,$period,$product){
        if ($period==1) {
            $query = DB::table('mean_square_holt_errors')->where('mean_square_holt',$product)->where('month_mean_square_holt','0')->select('level_mean_square_holt','trend_mean_square_holt')->first();
            $index=0;
            foreach ($query as $t){
                if ($index==0) $level = $t;
                if ($index==1) $trend = $t;
                $index ++;
            }
        }
        $alfa=0;
        for ($i=1;$i<10;$i++){
            $alfa = $alfa + 0.1;
            $alfa = round($alfa,1);
            $beta = 0;
            for ($k=1;$k<10;$k++){
                $beta = $beta + 0.1;
                $beta = round($beta,1);
                if ($period>1) {
                        $query = DB::table('mean_square_holt_errors')->where('mean_square_holt',$product)->where('alfa_mean_square_holt',$alfa)->where('beta_mean_square_holt',$beta)->where('month_mean_square_holt',$period-1)->select('level_mean_square_holt','trend_mean_square_holt')->first();
                        $index=0;
                        foreach ($query as $t){
                            if ($index==0) $level = $t;
                            if ($index==1) $trend = $t;
                            $index ++;
                        }
                }
                $newLevel = $this->CalculateLevel($sales,$level,$trend,$alfa,1);
                $newTrend = $this->CalculateTrend($newLevel,$level,$trend,$beta);
                $forecast = ($newTrend * $period) + $newLevel;
                if ($forecast<=0) $forecast = 0;
                $unit = DB::table('sales_lists')->where('id_sales_list', $product)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
                if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $forecast = round($forecast);
                $error = pow(abs($forecast - $sales),2); //Calcoliamo il quadrato dell'errore
                MeanSquareHoltError::create(
                        [
                            'mean_square_holt' => $product,
                            'alfa_mean_square_holt' => $alfa,
                            'beta_mean_square_holt' => $beta,
                            'level_mean_square_holt' => $newLevel,
                            'trend_mean_square_holt' => $newTrend,
                            'month_mean_square_holt' => $period,
                            'mean_square_holt_error' => $error,
                        ]
                    );
            }
        }
        if ($period<12) {
            $accessdate=date("Y-m-d");
            $date_booking = strtotime('+1 month',strtotime($accessdate));
            $date_booking = date ('Y-m-1', $date_booking);
            $create = BatchForecastRevision::create(
                [
                    'forecast_revision' => $product,
                    'RevisionForecastModel' => '01',
                    'period' => $period+2,
                    'booking_revision_forecast' => $date_booking
                ]);
        } else {
            $accessdate=date("Y-m-d");
            $create = BatchRevisionParameter::create(
                [
                    'revision_parameter' => $product,
                    'revision_parameter_forecast_model' => '01',
                    'booking_revision_parameter' => $accessdate
                ]
            );
        }
        if ($create) {
            if ($period==1) DB::table('mean_square_holt_errors')->where('mean_square_holt',$product)->where('month_mean_square_holt','0')->delete();
            return true;
        }
        else return false;
    }

    public function ProcessParametersWinter2($sales,$period,$product){
        if ($period>2) $select = 'factor'.($period-2).'_mean_square_winter2';
        else {
            $select = 'factor'.($period).'_mean_square_winter2';
            $query = DB::table('mean_square_winter2_errors')->where('month_mean_square_winter2',$period+4)->select($select)->first();
            foreach ($query as $t) $factor = $t;
        }
        if ($period==1) {
            $query = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->where('month_mean_square_winter2','5')->select('level_mean_square_winter2','trend_mean_square_winter2')->first();
            $index=0;
            foreach ($query as $t){
                if ($index==0) $level = $t;
                if ($index==1) $trend = $t;
                $index ++;
            }
        }
        $alfa=0;
        for ($i=1;$i<10;$i++){
            $alfa = $alfa + 0.1;
            $alfa = round($alfa,1);
            $beta = 0;
            for ($k=1;$k<10;$k++){
                $beta = $beta + 0.1;
                $beta = round($beta,1);
                $gamma=0;
                for ($z=1;$z<10;$z++){
                    $gamma = $gamma + 0.1;
                    $gamma = round($gamma,1);
                    if ($period>1) {
                        $query = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->where('alfa_mean_square_winter2',$alfa)->where('beta_mean_square_winter2',$beta)->where('gamma_mean_square_winter2',$gamma)->where('month_mean_square_winter2',$period-1)->select('level_mean_square_winter2','trend_mean_square_winter2')->first();
                        $index=0;
                        foreach ($query as $t){
                            if ($index==0) $level = $t;
                            if ($index==1) $trend = $t;
                            $index ++;
                        }
                    }
                    if ($period>2) {
                        $query = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->where('alfa_mean_square_winter2',$alfa)->where('beta_mean_square_winter2',$beta)->where('gamma_mean_square_winter2',$gamma)->where('month_mean_square_winter2',$period-2)->select($select)->first();
                        foreach ($query as $t) $factor = $t;
                    }
                    $newLevel = $this->CalculateLevel($sales/6,$level,$trend,$alfa,$factor);
                    $newTrend = $this->CalculateTrend($newLevel,$level,$trend,$beta);
                    $newFactor = $this->CalculateFactor($sales/6,$newLevel,$gamma,$factor);
                    $forecast = $this->DevelopsForecast(1,6,$newLevel,$newTrend,$factor);
                    $unit = DB::table('sales_lists')->where('id_sales_list', $product)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
                    if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $forecast = $this->RoundsUpForecast($forecast);
                    $total = $forecast[1] * 6;
                    $error = pow(abs($total - $sales),2); //Calcoliamo il quadrato dell'errore
                    MeanSquareWinter2Error::create(
                      [
                          'mean_square_winter2' => $product,
                          'alfa_mean_square_winter2' => $alfa,
                          'beta_mean_square_winter2' => $beta,
                          'gamma_mean_square_winter2' => $gamma,
                          'level_mean_square_winter2' => $newLevel,
                          'trend_mean_square_winter2' => $newTrend,
                          $select => $newFactor,
                          'month_mean_square_winter2' => $period,
                          'mean_square_winter2_error' => $error,
                      ]
                    );
                }
            }
        }
        if ($period<4) {
            $accessdate=date("Y-m-d");
            $date_booking = strtotime('+6 month',strtotime($accessdate));
            $date_booking = date ('Y-m-1', $date_booking);
            $create = BatchForecastRevision::create(
            [
            'forecast_revision' => $product,
            'RevisionForecastModel' => '10',
            'period' => $period+2,
            'booking_revision_forecast' => $date_booking
            ]);
        } else {
            $accessdate=date("Y-m-d");
            $create = BatchRevisionParameter::create(
              [
                  'revision_parameter' => $product,
                  'revision_parameter_forecast_model' => '10',
                  'booking_revision_parameter' => $accessdate
              ]
            );
        }
        if ($create){
            if ($period<3) DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->where('month_mean_square_winter2',$period+4)->delete();
            return true;
        } else return false;
    }

    public function ProcessParametersWinter4($sales,$period,$product){
        if ($period>4) $select = 'factor'.($period-4).'_mean_square_winter4';
        else {
            $select = 'factor'.($period).'_mean_square_winter4';
            $query = DB::table('mean_square_winter4_errors')->where('month_mean_square_winter4',$period+8)->select($select)->first();
            foreach ($query as $t) $factor = $t;
        }

        if ($period==1) {
            $query = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('month_mean_square_winter4','9')->select('level_mean_square_winter4','trend_mean_square_winter4')->first();
            $index=0;
            foreach ($query as $t){
                if ($index==0) $level = $t;
                if ($index==1) $trend = $t;
                $index ++;
            }
        }
        $alfa=0;
        for ($i=1;$i<10;$i++){
            $alfa = $alfa + 0.1;
            $alfa = round($alfa,1);
            $beta = 0;
            for ($k=1;$k<10;$k++){
                $beta = $beta + 0.1;
                $beta = round($beta,1);
                $gamma=0;
                for ($z=1;$z<10;$z++){
                    $gamma = $gamma + 0.1;
                    $gamma = round($gamma,1);
                    if ($period>1) {
                        $query = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('alfa_mean_square_winter4',$alfa)->where('beta_mean_square_winter4',$beta)->where('gamma_mean_square_winter4',$gamma)->where('month_mean_square_winter4',$period-1)->select('level_mean_square_winter4','trend_mean_square_winter4')->first();
                        $index=0;
                        foreach ($query as $t){
                            if ($index==0) $level = $t;
                            if ($index==1) $trend = $t;
                            $index ++;
                        }
                    }
                    if ($period>4) {
                        $query = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('alfa_mean_square_winter4',$alfa)->where('beta_mean_square_winter4',$beta)->where('gamma_mean_square_winter4',$gamma)->where('month_mean_square_winter4',$period-4)->select($select)->first();
                        foreach ($query as $t) $factor = $t;
                    }
                    $newLevel = $this->CalculateLevel($sales/3,$level,$trend,$alfa,$factor);
                    $newTrend = $this->CalculateTrend($newLevel,$level,$trend,$beta);
                    $newFactor = $this->CalculateFactor($sales/3,$newLevel,$gamma,$factor);
                    $forecast = $this->DevelopsForecast(1,3,$newLevel,$newTrend,$factor);
                    $unit = DB::table('sales_lists')->where('id_sales_list', $product)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
                    if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $forecast = $this->RoundsUpForecast($forecast);
                    $total = $forecast[1] * 3;
                    $error = pow(abs($total - $sales),2); //Calcoliamo il quadrato dell'errore
                    MeanSquareWinter4Error::create(
                        [
                            'mean_square_winter4' => $product,
                            'alfa_mean_square_winter4' => $alfa,
                            'beta_mean_square_winter4' => $beta,
                            'gamma_mean_square_winter4' => $gamma,
                            'level_mean_square_winter4' => $newLevel,
                            'trend_mean_square_winter4' => $newTrend,
                            $select => $newFactor,
                            'month_mean_square_winter4' => $period,
                            'mean_square_winter4_error' => $error,
                        ]
                    );
                }
            }
        }
        if ($period<8) {
            $accessdate=date("Y-m-d");
            $date_booking = strtotime('+3 month',strtotime($accessdate));
            $date_booking = date ('Y-m-1', $date_booking);
            $create = BatchForecastRevision::create(
                [
                    'forecast_revision' => $product,
                    'RevisionForecastModel' => '11',
                    'period' => $period+2,
                    'booking_revision_forecast' => $date_booking
                ]);
        } else {
            $accessdate=date("Y-m-d");
            $create = BatchRevisionParameter::create(
                [
                    'revision_parameter' => $product,
                    'revision_parameter_forecast_model' => '11',
                    'booking_revision_parameter' => $accessdate
                ]
            );
        }
        if ($create){
            if ($period<5) DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('month_mean_square_winter4',$period+8)->delete();
            return true;
        } else return false;
    }

    public function RevisionParameters(){
        $system_time = date('Y-m-d');
        $works = DB::table('batch_revision_parameters')->where('executed_revision_parameter','0')->where('booking_revision_parameter','<=',$system_time)->select('*')->get();
        if($works!=null) {
            foreach ($works as $work) {
                if ($work->revision_parameter_forecast_model=="01") $process = $this->RevisionParametersHolt($work->revision_parameter);
                if ($work->revision_parameter_forecast_model=="10") $process = $this->RevisionParametersWinter2($work->revision_parameter);
                if ($work->revision_parameter_forecast_model=="11") $process = $this->RevisionParametersWinter4($work->revision_parameter);
                if ($process){
                    DB::table('batch_revision_parameters')->where('id_revision_parameter',$work->id_revision_parameter)->update(
                        [
                            'executed_revision_parameter' => '1'
                        ]
                    );
                }
            } return true;
        } else return false;
    }

    public function RevisionParametersHolt($product){
        $alfa=0;
        $meanSquareError=null;
        for ($k = 1; $k < 10; $k++) {
            $alfa = $alfa + 0.1;
            $alfa = round($alfa, 1);
            $beta = 0;
            for ($z = 1; $z < 10; $z++) {
                $beta = $beta + 0.1;
                $beta = round($beta, 1);
                $error = DB::table('mean_square_holt_errors')->where('mean_square_holt',$product)->where('alfa_mean_square_holt',$alfa)->where('beta_mean_square_holt',$beta)->sum('mean_square_holt_error');
                $error = $error/12;
                if ($alfa==0.1 and $beta==0.1) {
                    $meanSquareError=$error;
                    $parameter['alfa']=$alfa;
                    $parameter['beta']=$beta;
                }
                if ($error<=$meanSquareError){
                    $meanSquareError=$error;
                    $parameter['alfa']=$alfa;
                    $parameter['beta']=$beta;
                }
            }
        }
        $new = DB::table('mean_square_holt_errors')->where('mean_square_holt',$product)->where('alfa_mean_square_holt',$parameter['alfa'])->where('beta_mean_square_holt',$parameter['beta'])->where('month_mean_square_holt','12')->select('level_mean_square_holt','trend_mean_square_holt')->first();
        $revisione = $this->DevelopsForecast(1, 12, $new->level_mean_square_holt, $new->trend_mean_square_holt, null);
        $unit = DB::table('sales_lists')->where('id_sales_list', $product)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
        if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $revisione = $this->RoundsUpForecast($revisione);

        //Salvataggio vendite ultimo anno nella tabella Historical Datas
        $save = DB::table('sales_lists')->where('id_sales_list',$product)->select('initial_month_sales','company_sales_list','1','2','3','4','5','6','7','8','9','10','11','12')->first();
        $i=1;
        $k=1;
        foreach ($save as $t){
            if ($i>2) {
                $sales[$k]=$t;
                $k++;
            }
            $i++;
        }



        DB::table('forecast_holt_models')->where('ForecastHoltProduct',$product)->delete();
        $create = ForecastHoltModel::create(
            [
                'ForecastHoltProduct' => $product,
                'alfa_holt' => $parameter['alfa'],
                'beta_holt' => $parameter['beta'],
                'level_holt' => $new->level_mean_square_holt,
                'trend_holt' => $new->trend_mean_square_holt,
                '1' => $revisione[1],
                '2' => $revisione[2],
                '3' => $revisione[3],
                '4' => $revisione[4],
                '5' => $revisione[5],
                '6' => $revisione[6],
                '7' => $revisione[7],
                '8' => $revisione[8],
                '9' => $revisione[9],
                '10' => $revisione[10],
                '11' => $revisione[11],
                '12' => $revisione[12],
                'initial_month_holt' => $save->initial_month_sales
            ]
        );
        if ($create) {
            DB::table('sales_lists')->where('id_sales_list',$product)->update(
                [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0',
                    '6' => '0',
                    '7' => '0',
                    '8' => '0',
                    '9' => '0',
                    '10' => '0',
                    '11' => '0',
                    '12' => '0',
                ]
            );

            DB::table('historical_datas')->where('product_historical_data',$product)->where('company_historical_data',$save->company_sales_list)->update(
                [
                    '1' => $sales[1],
                    '2' => $sales[2],
                    '3' => $sales[3],
                    '4' => $sales[4],
                    '5' => $sales[5],
                    '6' => $sales[6],
                    '7' => $sales[7],
                    '8' => $sales[8],
                    '9' => $sales[9],
                    '10' => $sales[10],
                    '11' => $sales[11],
                    '12' => $sales[12],
                    'initial_month' => $save->initial_month_sales,
                ]
            );


            DB::table('mean_square_holt_errors')->where('mean_square_holt',$product)->delete();

            MeanSquareHoltError::create(
                [
                    'mean_square_holt' => $product,
                    'level_mean_square_holt' => $new->level_mean_square_holt,
                    'trend_mean_square_holt' => $new->trend_mean_square_holt,
                    'month_mean_square_holt' => 0
                ]
            );

            $accessdate=date("Y-m-d");
            $date_booking = strtotime('+1 month',strtotime($accessdate));
            $date_booking = date ('Y-m-1', $date_booking);

            //Prenotazione operazione di Revisione previsione
            BatchForecastRevision::create(
                [
                    'forecast_revision' => $product,
                    'RevisionForecastModel' => '01',
                    'period' => 2,
                    'booking_revision_forecast' => $date_booking
                ]
            );



            //Condivisione previsione con i fornitori
            $shares = DB::table('supply_chains')->where('company_supply_shares',$save->company_sales_list)->where('forecast','1')->get();
            if (count($shares)>0){
                foreach ($shares as $create){
                    BatchSharingForecast::create(
                        [
                            'sharing_forecast' => $create->id_supply_chain,
                            'sharing_product' => $product,
                            'sharing_forecast_model' => '01',
                            'booking_sharing_forecast' => $accessdate

                        ]
                    );
                }
            }
            return true;
        } else return false;
    }

    public function RevisionParametersWinter2($product){
        $alfa=0;
        $meanSquareError=null;
        for ($i=1;$i<10;$i++) {
            $alfa = $alfa + 0.1;
            $alfa = round($alfa, 1);
            $beta = 0;
            for ($k = 1; $k < 10; $k++) {
                $beta = $beta + 0.1;
                $beta = round($beta, 1);
                $gamma = 0;
                for ($z = 1; $z < 10; $z++) {
                    $gamma = $gamma + 0.1;
                    $gamma = round($gamma, 1);
                    $error = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->where('alfa_mean_square_winter2',$alfa)->where('beta_mean_square_winter2',$beta)->where('gamma_mean_square_winter2',$gamma)->sum('mean_square_winter2_error');
                    $error = $error/4;
                    if ($alfa==0.1 and $beta==0.1 and $gamma==0.1) {
                        $meanSquareError=$error;
                        $parameter['alfa']=$alfa;
                        $parameter['beta']=$beta;
                        $parameter['gamma']=$gamma;
                    }
                    if ($error<=$meanSquareError){
                        $meanSquareError=$error;
                        $parameter['alfa']=$alfa;
                        $parameter['beta']=$beta;
                        $parameter['gamma']=$gamma;
                    }
                }
            }
        }
        $new = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->where('alfa_mean_square_winter2',$parameter['alfa'])->where('beta_mean_square_winter2',$parameter['beta'])->where('gamma_mean_square_winter2',$parameter['gamma'])->where('month_mean_square_winter2','4')->select('level_mean_square_winter2','trend_mean_square_winter2','factor2_mean_square_winter2')->first();
        $factor1 = DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->where('alfa_mean_square_winter2',$parameter['alfa'])->where('beta_mean_square_winter2',$parameter['beta'])->where('gamma_mean_square_winter2',$parameter['gamma'])->where('month_mean_square_winter2','3')->select('factor1_mean_square_winter2')->first();

        $factors[1]=$factor1->factor1_mean_square_winter2;
        $factors[2]=$new->factor2_mean_square_winter2;
        $revisione = $this->DevelopsForecast(2, 12, $new->level_mean_square_winter2, $new->trend_mean_square_winter2, $factors);
        $unit = DB::table('sales_lists')->where('id_sales_list', $product)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
        if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $revisione = $this->RoundsUpForecast($revisione);

        //Salvataggio vendite ultimo anno nella tabella Historical Datas
        $save = DB::table('sales_lists')->where('id_sales_list',$product)->select('initial_month_sales','company_sales_list','1','2','3','4','5','6','7','8','9','10','11','12')->first();
        $i=1;
        $k=1;
        foreach ($save as $t){
            if ($i>2) {
                $sales[$k]=$t;
                $k++;
            }
            $i++;
        }



        DB::table('forecast_winter2_models')->where('Forecastwinter2Product',$product)->delete();
        $create = ForecastWinter2Model::create(
            [
                'Forecastwinter2Product' => $product,
                'alfa_winter2' => $parameter['alfa'],
                'beta_winter2' => $parameter['beta'],
                'gamma_winter2' => $parameter['gamma'],
                'level_winter2' => $new->level_mean_square_winter2,
                'trend_winter2' => $new->trend_mean_square_winter2,
                'factor1_winter2' => $factors[1],
                'factor2_winter2' => $factors[2],
                '1' => $revisione[1]*6,
                '2' => $revisione[7]*6,
                '3' => $revisione[1]*6,
                '4' => $revisione[7]*6,
                'initial_month_winter2' => $save->initial_month_sales
            ]
        );
        if ($create) {
            DB::table('sales_lists')->where('id_sales_list',$product)->update(
                [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0',
                    '6' => '0',
                    '7' => '0',
                    '8' => '0',
                    '9' => '0',
                    '10' => '0',
                    '11' => '0',
                    '12' => '0',
                ]
            );

            DB::table('historical_datas')->where('product_historical_data',$product)->where('company_historical_data',$save->company_sales_list)->update(
                [
                    '1' => $sales[1],
                    '2' => $sales[2],
                    '3' => $sales[3],
                    '4' => $sales[4],
                    '5' => $sales[5],
                    '6' => $sales[6],
                    '7' => $sales[7],
                    '8' => $sales[8],
                    '9' => $sales[9],
                    '10' => $sales[10],
                    '11' => $sales[11],
                    '12' => $sales[12],
                    'initial_month' => $save->initial_month_sales,
                ]
            );


            DB::table('mean_square_winter2_errors')->where('mean_square_winter2',$product)->delete();

            $regression['level']=$new->level_mean_square_winter2;
            $regression['trend']=$new->trend_mean_square_winter2;
            $this->CreateMeanSquareWinter2Error($product, $regression, 5, $factors[1],'factor1_mean_square_winter2');
            $this->CreateMeanSquareWinter2Error($product, $regression, 6, $factors[2],'factor2_mean_square_winter2');
            $accessdate=date("Y-m-d");
            $date_booking = strtotime('+6 month',strtotime($accessdate));
            $date_booking = date ('Y-m-1', $date_booking);

            //Prenotazione operazione di Revisione previsione
            BatchForecastRevision::create(
                [
                    'forecast_revision' => $product,
                    'RevisionForecastModel' => '10',
                    'period' => 2,
                    'booking_revision_forecast' => $date_booking
                ]
            );



            //Condivisione previsione con i fornitori
            $shares = DB::table('supply_chains')->where('company_supply_shares',$save->company_sales_list)->where('forecast','1')->get();
            if (count($shares)>0){
                foreach ($shares as $create){
                    BatchSharingForecast::create(
                        [
                            'sharing_forecast' => $create->id_supply_chain,
                            'sharing_product' => $product,
                            'sharing_forecast_model' => '10',
                            'booking_sharing_forecast' => $accessdate

                        ]
                    );
                }
            }
            return true;
        } else return false;
    }

    public function RevisionParametersWinter4($product){
        $alfa=0;
        $meanSquareError=null;
        for ($i=1;$i<10;$i++) {
            $alfa = $alfa + 0.1;
            $alfa = round($alfa, 1);
            $beta = 0;
            for ($k = 1; $k < 10; $k++) {
                $beta = $beta + 0.1;
                $beta = round($beta, 1);
                $gamma = 0;
                for ($z = 1; $z < 10; $z++) {
                    $gamma = $gamma + 0.1;
                    $gamma = round($gamma, 1);
                    $error = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('alfa_mean_square_winter4',$alfa)->where('beta_mean_square_winter4',$beta)->where('gamma_mean_square_winter4',$gamma)->sum('mean_square_winter4_error');
                    $error = $error/8;
                    if ($alfa==0.1 and $beta==0.1 and $gamma==0.1) {
                        $meanSquareError=$error;
                        $parameter['alfa']=$alfa;
                        $parameter['beta']=$beta;
                        $parameter['gamma']=$gamma;
                    }
                    if ($error<=$meanSquareError){
                        $meanSquareError=$error;
                        $parameter['alfa']=$alfa;
                        $parameter['beta']=$beta;
                        $parameter['gamma']=$gamma;
                    }
                }
            }
        }
        $new = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('alfa_mean_square_winter4',$parameter['alfa'])->where('beta_mean_square_winter4',$parameter['beta'])->where('gamma_mean_square_winter4',$parameter['gamma'])->where('month_mean_square_winter4','8')->select('level_mean_square_winter4','trend_mean_square_winter4','factor4_mean_square_winter4')->first();
        $factor1 = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('alfa_mean_square_winter4',$parameter['alfa'])->where('beta_mean_square_winter4',$parameter['beta'])->where('gamma_mean_square_winter4',$parameter['gamma'])->where('month_mean_square_winter4','5')->select('factor1_mean_square_winter4')->first();
        $factor2 = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('alfa_mean_square_winter4',$parameter['alfa'])->where('beta_mean_square_winter4',$parameter['beta'])->where('gamma_mean_square_winter4',$parameter['gamma'])->where('month_mean_square_winter4','6')->select('factor2_mean_square_winter4')->first();
        $factor3 = DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->where('alfa_mean_square_winter4',$parameter['alfa'])->where('beta_mean_square_winter4',$parameter['beta'])->where('gamma_mean_square_winter4',$parameter['gamma'])->where('month_mean_square_winter4','7')->select('factor3_mean_square_winter4')->first();

        $factors[1]=$factor1->factor1_mean_square_winter4;
        $factors[2]=$factor2->factor2_mean_square_winter4;
        $factors[3]=$factor3->factor3_mean_square_winter4;
        $factors[4]=$new->factor4_mean_square_winter4;
        $revisione = $this->DevelopsForecast(4, 12, $new->level_mean_square_winter4, $new->trend_mean_square_winter4, $factors);
        $unit = DB::table('sales_lists')->where('id_sales_list', $product)->leftJoin('inventories', 'inventory_sales_list', '=', 'id_inventory')->leftJoin('productions', 'production_sales_list', '=', 'id_production')->select('unit_production', 'unit_inventory')->first();
        if ($unit->unit_production == 'NR' or $unit->unit_inventory == 'NR') $revisione = $this->RoundsUpForecast($revisione);

        //Salvataggio vendite ultimo anno nella tabella Historical Datas
        $save = DB::table('sales_lists')->where('id_sales_list',$product)->select('initial_month_sales','company_sales_list','1','2','3','4','5','6','7','8','9','10','11','12')->first();
        $i=1;
        $k=1;
        foreach ($save as $t){
            if ($i>2) {
                $sales[$k]=$t;
                $k++;
            }
            $i++;
        }



        DB::table('forecast_winter4_models')->where('Forecastwinter4Product',$product)->delete();
        $create = ForecastWinter4Model::create(
            [
                'Forecastwinter4Product' => $product,
                'alfa_winter4' => $parameter['alfa'],
                'beta_winter4' => $parameter['beta'],
                'gamma_winter4' => $parameter['gamma'],
                'level_winter4' => $new->level_mean_square_winter4,
                'trend_winter4' => $new->trend_mean_square_winter4,
                'factor1' => $factors[1],
                'factor2' => $factors[2],
                'factor3' => $factors[3],
                'factor4' => $factors[4],
                '1' => $revisione[1]*3,
                '2' => $revisione[4]*3,
                '3' => $revisione[7]*3,
                '4' => $revisione[10]*3,
                '5' => $revisione[1]*3,
                '6' => $revisione[4]*3,
                '7' => $revisione[7]*3,
                '8' => $revisione[10]*3,
                'initial_month_winter4' => $save->initial_month_sales
            ]
        );
        if ($create) {
            DB::table('sales_lists')->where('id_sales_list',$product)->update(
                [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0',
                    '6' => '0',
                    '7' => '0',
                    '8' => '0',
                    '9' => '0',
                    '10' => '0',
                    '11' => '0',
                    '12' => '0',
                ]
            );

            DB::table('historical_datas')->where('product_historical_data',$product)->where('company_historical_data',$save->company_sales_list)->update(
                [
                    '1' => $sales[1],
                    '2' => $sales[2],
                    '3' => $sales[3],
                    '4' => $sales[4],
                    '5' => $sales[5],
                    '6' => $sales[6],
                    '7' => $sales[7],
                    '8' => $sales[8],
                    '9' => $sales[9],
                    '10' => $sales[10],
                    '11' => $sales[11],
                    '12' => $sales[12],
                    'initial_month' => $save->initial_month_sales,
                ]
            );


            DB::table('mean_square_winter4_errors')->where('mean_square_winter4',$product)->delete();

            $regression['level']=$new->level_mean_square_winter4;
            $regression['trend']=$new->trend_mean_square_winter4;
            $this->CreateMeanSquareWinter4Error($product, $regression, 9, $factors[1],'factor1_mean_square_winter4');
            $this->CreateMeanSquareWinter4Error($product, $regression, 10, $factors[2],'factor2_mean_square_winter4');
            $this->CreateMeanSquareWinter4Error($product, $regression, 11, $factors[3],'factor3_mean_square_winter4');
            $this->CreateMeanSquareWinter4Error($product, $regression, 12, $factors[4],'factor4_mean_square_winter4');
            $accessdate=date("Y-m-d");
            $date_booking = strtotime('+3 month',strtotime($accessdate));
            $date_booking = date ('Y-m-1', $date_booking);

            //Prenotazione operazione di Revisione previsione
            BatchForecastRevision::create(
                [
                    'forecast_revision' => $product,
                    'RevisionForecastModel' => '11',
                    'period' => 2,
                    'booking_revision_forecast' => $date_booking
                ]
            );



            //Condivisione previsione con i fornitori
            $shares = DB::table('supply_chains')->where('company_supply_shares',$save->company_sales_list)->where('forecast','1')->get();
            if (count($shares)>0){
                foreach ($shares as $create){
                    BatchSharingForecast::create(
                        [
                            'sharing_forecast' => $create->id_supply_chain,
                            'sharing_product' => $product,
                            'sharing_forecast_model' => '11',
                            'booking_sharing_forecast' => $accessdate

                        ]
                    );
                }
            }
           return true;
        } else return false;
     }

     public function SharingForecast(){
         $system_time = date('Y-m-d');
         $works = DB::table('batch_sharing_forecasts')->where('executed_sharing_forecast','0')->where('booking_sharing_forecast','<=',$system_time)->select('sharing_forecast')->groupBy('sharing_forecast')->get();
         if($works!=null) {
                foreach ($works as $work){
                    $supply = DB::table('supply_chains')->where('id_supply_chain',$work->sharing_forecast)->join('company_offices','id_company_office','=','company_supply_shares')->join('company_offices as company2','company2.id_company_office','=','company_supply_received')->join('comuni as comune2','comune2.id_comune','=','company2.cap_company')->join('comuni as comune1','comune1.id_comune','=','company_offices.cap_company')->leftjoin('company_offices_extra_italia as extra1','extra1.company_office','=','company_supply_shares')->leftjoin('company_offices_extra_italia as extra2','extra2.company_office','=','company_supply_received')->select('id_supply_chain','company_supply_shares','company_supply_received','ean_mapping','company_offices.rag_soc_company as rag_soc_shares','company_offices.indirizzo_company as indirizzo_shares','company_offices.civico_company as civico_shares','comune1.cap as cap_shares','comune1.comune as comune_shares','comune1.sigla_prov as sigla_prov_shares','extra1.cap_company_office_extra as cap_extra1','extra1.city_company_office_extra as city_extra1','extra1.state_company_office_extra as state_extra1','company_offices.email_company as email_shares','company2.rag_soc_company as rag_soc_received','company2.indirizzo_company as indirizzo_received','company2.civico_company as civico_received','comune2.cap as cap_received','comune2.comune as comune_received','comune2.sigla_prov as sigla_prov_received','company2.email_company as email_received','extra2.cap_company_office_extra as cap_extra2','extra2.city_company_office_extra as city_extra2','extra2.state_company_office_extra as state_extra2')->first();
                    $id_provider = DB::table('providers')->where('company_provider',$supply->company_supply_shares)->where('provider_supply',$supply->company_supply_received)->select('id_provider')->first();
                    $product = DB::table('batch_sharing_forecasts')->where('executed_sharing_forecast','0')->where('booking_sharing_forecast','<=',$system_time)->where('sharing_forecast',$work->sharing_forecast)->where('sharing_forecast_model','01')->join('sales_lists','id_sales_list','=','sharing_product')->join('inventories','id_inventory','=','inventory_sales_list')->join('forecast_holt_models','ForecastHoltProduct','=','sharing_product')->join('mapping_inventory_providers','inventory_mapping_provider','=','id_inventory')->where('provider_mapping_provider',$id_provider->id_provider)->select('id_sales_list','initial_month_sales','cod_inventory','title_inventory','unit_inventory','stock','committed','arriving','ean_inventory','cod_mapping_inventory_provider','forecast_holt_models.1 as p1','forecast_holt_models.2 as p2','forecast_holt_models.3 as p3','forecast_holt_models.4 as p4','forecast_holt_models.5 as p5','forecast_holt_models.6 as p6','forecast_holt_models.7 as p7','forecast_holt_models.8 as p8','forecast_holt_models.9 as p9','forecast_holt_models.10 as p10','forecast_holt_models.11 as p11','forecast_holt_models.12 as p12')->get();
                    $production = DB::table('batch_sharing_forecasts')->where('executed_sharing_forecast','0')->where('booking_sharing_forecast','<=',$system_time)->where('sharing_forecast',$work->sharing_forecast)->where('sharing_forecast_model','01')->join('sales_lists','id_sales_list','=','sharing_product')->join('mapping_inventory_productions','production_map_pro','=','production_sales_list')->join('inventories','id_inventory','=','inventory_map_pro')->join('forecast_holt_models','ForecastHoltProduct','=','sharing_product')->join('mapping_inventory_providers','inventory_mapping_provider','=','id_inventory')->where('provider_mapping_provider',$id_provider->id_provider)->select('id_sales_list','initial_month_sales','cod_inventory','title_inventory','unit_inventory','stock','committed','arriving','ean_inventory','cod_mapping_inventory_provider','quantity_mapping_production as q','forecast_holt_models.1 as p1','forecast_holt_models.2 as p2','forecast_holt_models.3 as p3','forecast_holt_models.4 as p4','forecast_holt_models.5 as p5','forecast_holt_models.6 as p6','forecast_holt_models.7 as p7','forecast_holt_models.8 as p8','forecast_holt_models.9 as p9','forecast_holt_models.10 as p10','forecast_holt_models.11 as p11','forecast_holt_models.12 as p12')->get();
                    $month = date('m');
                    if (count($product)>0){
                        $i=0;
                        foreach ($product as $t){
                            $begin = $this->monthForecast($t->initial_month_sales,$month);
                            $product[$i]->begin = $begin;
                            $i++;
                        }
                        for ($i=0;$i<count($product);$i++){
                            $k=1;
                            $x=0;
                            foreach ($product[$i] as $t){
                                if($x>9 and $x<22){
                                    $months[$k] = $t;
                                    $k++;
                                }
                                $x++;
                                if ($x==23) $begin = $t;
                            }
                            for ($j=1;$j<13;$j++){
                                if ($begin>12) $begin=$begin-12;
                                $prova[$j] = $months[$begin];
                                $begin++;
                            }
                            $product[$i]->p1 = $prova[1];
                            $product[$i]->p2 = $prova[2];
                            $product[$i]->p3 = $prova[3];
                            $product[$i]->p4 = $prova[4];
                            $product[$i]->p5 = $prova[5];
                            $product[$i]->p6 = $prova[6];
                            $product[$i]->p7 = $prova[7];
                            $product[$i]->p8 = $prova[8];
                            $product[$i]->p9 = $prova[9];
                            $product[$i]->p10 = $prova[10];
                            $product[$i]->p11 = $prova[11];
                            $product[$i]->p12 = $prova[12];
                        }
                    }
                    if (count($production)>0){
                        $i=0;
                        foreach ($production as $t){
                            $begin = $this->monthForecast($t->initial_month_sales,$month);
                            $production[$i]->begin = $begin;
                            $production[$i]->p1 = $t->p1* $t->q;
                            $production[$i]->p2 = $t->p2* $t->q;
                            $production[$i]->p3 = $t->p3* $t->q;
                            $production[$i]->p4 = $t->p4* $t->q;
                            $production[$i]->p5 = $t->p5* $t->q;
                            $production[$i]->p6 = $t->p6* $t->q;
                            $production[$i]->p7 = $t->p7* $t->q;
                            $production[$i]->p8 = $t->p8* $t->q;
                            $production[$i]->p9 = $t->p9* $t->q;
                            $production[$i]->p10 = $t->p10* $t->q;
                            $production[$i]->p11 = $t->p11* $t->q;
                            $production[$i]->p12 = $t->p12* $t->q;
                            $i++;
                        }
                        for ($i=0;$i<count($production);$i++){
                            $k=1;
                            $x=0;
                            foreach ($production[$i] as $t){
                                if($x>10 and $x<23){
                                    $months[$k] = $t;
                                    $k++;
                                }
                                $x++;
                                if ($x==24) $begin = $t;
                            }
                            for ($j=1;$j<13;$j++){
                                if ($begin>12) $begin=$begin-12;
                                $prova[$j] = $months[$begin];
                                $begin++;
                            }
                            $production[$i]->p1 = $prova[1];
                            $production[$i]->p2 = $prova[2];
                            $production[$i]->p3 = $prova[3];
                            $production[$i]->p4 = $prova[4];
                            $production[$i]->p5 = $prova[5];
                            $production[$i]->p6 = $prova[6];
                            $production[$i]->p7 = $prova[7];
                            $production[$i]->p8 = $prova[8];
                            $production[$i]->p9 = $prova[9];
                            $production[$i]->p10 = $prova[10];
                            $production[$i]->p11 = $prova[11];
                            $production[$i]->p12 = $prova[12];
                        }
                    }
                    $how_product = count($product);
                    $how_production = count($production);
                    if ($how_production>0){
                        if ($how_product==0) $item = $production;
                        else {
                            $item = $product;
                            $k=$how_product;
                            foreach ($production as $t){
                                $ins = false;
                                $i=0;
                                foreach ($item as $value){
                                    if ($value->cod_inventory==$t->cod_inventory){
                                        $ins = true;
                                        $item[$i]->p1 = ($item[$i]->p1) + $t->p1;
                                        $item[$i]->p2 = ($item[$i]->p2) + $t->p2;
                                        $item[$i]->p3 = ($item[$i]->p3) + $t->p3;
                                        $item[$i]->p4 = ($item[$i]->p4) + $t->p4;
                                        $item[$i]->p5 = ($item[$i]->p5) + $t->p5;
                                        $item[$i]->p6 = ($item[$i]->p6) + $t->p6;
                                        $item[$i]->p7 = ($item[$i]->p7) + $t->p7;
                                        $item[$i]->p8 = ($item[$i]->p8) + $t->p8;
                                        $item[$i]->p9 = ($item[$i]->p9) + $t->p9;
                                        $item[$i]->p10 = ($item[$i]->p10) + $t->p10;
                                        $item[$i]->p11 = ($item[$i]->p11) + $t->p11;
                                        $item[$i]->p12 = ($item[$i]->p12) + $t->p12;
                                    }
                                    if ($ins) break;
                                    $i++;
                                }
                                if ($ins==false){
                                    $item[$k] = $t;
                                    $k++;
                                }
                            }
                        }
                    } else $item = $product;
                    $today = date('d/m/Y');
                    $filename = $this->pdfview2($item,'Sharing_Forecast_','SharingForecast',$supply, $today);
                    Mail::to($supply->email_received)->send(new SharingForecast($filename,$supply->rag_soc_received,$supply->rag_soc_shares,$supply->rag_soc_received));
                    Mail::to($supply->email_shares)->send(new SharingForecast($filename,$supply->rag_soc_shares,$supply->rag_soc_shares,$supply->rag_soc_received));
                    $del = unlink($filename);
                    if ($del){
                        DB::table('batch_sharing_forecasts')->where('sharing_forecast',$work->sharing_forecast)->where('executed_sharing_forecast','0')->update(
                            [
                                'executed_sharing_forecast' => '1'
                            ]
                        );
                    }
                } return true;
         } else return false;
     }

    public function pdfview2($data,$view,$controller,$supply,$today){
        $pdf = App::make('dompdf.wrapper')->setPaper('a4','landscape');
        $pdf->loadView('pdf.'.$controller, ['items' => $data, 'supply' => $supply, 'today' => $today]);
        $time=date('d-m-Y');
        $filename = $view .' del '.$time.' di '. $supply->rag_soc_shares .'.pdf';
        $pdf->save($filename);
        return $filename;
    }

     function monthForecast($initial,$month){
        if ($initial==$month) return 1;
        if ($initial<$month) return $month - ($initial - 1);
        if ($initial>$month) return 13 -($initial - $month);
     }

}

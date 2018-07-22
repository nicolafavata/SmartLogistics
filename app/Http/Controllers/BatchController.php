<?php

namespace App\Http\Controllers;

use App\BatchHistoricalDataAnalysis;
use App\Mail\BatchHistoricalData;
use App\Models\BatchForecastRevision;
use App\Models\BatchGenerationForecast;
use App\Models\BatchHistoricalDataWork;
use App\Mail\BatchInventories;
use App\Models\BatchProcessParameter;
use App\Models\BatchRevisionParameter;
use App\Models\BatchSharingForecast;
use App\Models\ForecastExponentialModel;
use App\Models\ForecastHoltModel;
use App\Models\ForecastWinter2Model;
use App\Models\ForecastWinter4Model;
use App\Models\HistoricalData;
use App\Models\Inventory;
use App\Models\MeanSquareHoltError;
use App\Models\MeanSquareWinter2Error;
use App\Models\MeanSquareWinter4Error;
use App\Models\SalesList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\If_;
use function Sodium\increment;

class BatchController extends Controller
{
    public function verifyToken($token){
        $adminToken = DB::table('users')->where('email','info@smartlogis.it')->select('remember_token')->first();
        if ($adminToken->remember_token==$token){
            $this->CreateInventoryFromFile();
            $this->RevisionParameters();
            $this->HistoricalSeriesGeneration();
            $this->HistoricalSeriesAnalysis();
            $this->GenerationForecast();
            $this->RevisionForecast();
            $this->ProcessParameters();
            dd('previsione generata');
        } else return view('errors.500');
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
                                //controllo unità di misura
                                $block_unit = $this->FindUnit($data['4']);
                                if ($block_unit==false){
                                    $block_iva = $this->FindIvaCode($data['11']);
                                    if ($block_iva!=null){
                                        if (strlen($data['1']>80)) $data['1'] = substr($data['1'],0,80);
                                        if (strlen($data['2']>50)) $data['2'] = substr($data['2'],0,50);
                                        if (strlen($data['3']>50)) $data['3'] = substr($data['3'],0,50);
                                        if (strlen($data['18']>190)) $data['18'] = substr($data['18'],0,190);
                                        if (strlen($data['7']>30)) $data['7'] = substr($data['7'],0,30);
                                        if (strlen($data['8']>18)) $data['8'] = substr($data['8'],0,18);
                                        $data['5'] = round($data['5'],2);
                                        $data['12'] = round($data['12'],2);
                                        $data['13'] = round($data['13'],2);
                                        $data['14'] = round($data['14'],2);
                                        $data['15'] = round($data['15'],2);
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
                                            $push = SalesList::create([
                                                'company_sales_list' => $work->company_batch_inventory,
                                                'inventory_sales_list' => $create->id_inventory,
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
            }
        } else return false;
    }

    public function FindCodeAndBlock($table,$id_company,$company,$id_code,$code){
        $find = DB::table($table)->where($id_company,$company)->where($id_code,$code)->first();
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
                if ($work->RevisionForecastModel=='00'){
                    $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select('forecast_model')->first();
                    if ($query->forecast_model!=null){
                        if ($work->period>12) $period = $work->period - 12; else $period = $work->period;
                        $query = DB::table('sales_lists')->where('id_sales_list', $work->forecast_revision)->select($period)->first();
                        if ($query) foreach ($query as $t) $sales = $t;
                            if ($work->period=='1'){
                                // <-- A T T E N Z I O N E --> ALLA PRIMA VENDITA EFFETTUARE QUESTE OPERAZIONI !!!!
                                //Memorizziamo nella tabella 'sales_lists' e 'historical data' il mese iniziale
                           //     $initial_month = substr($system_time,5,2);
                           //     $initial_month--;
                           //     if($initial_month=='0') $initial_month='12';
                           //     DB::table('sales_lists')->where('id_sales_list',$work->forecast_revision)->update(
                           //         [
                           //             'initial_month_sales' => $initial_month,
                           //             'forecast_model' => '00'
                           //         ]
                           //     );

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


}

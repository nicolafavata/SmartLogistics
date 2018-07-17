<?php

namespace App\Http\Controllers;

use App\BatchHistoricalDataAnalysis;
use App\Mail\BatchHistoricalData;
use App\Models\BatchForecastRevision;
use App\Models\BatchGenerationForecast;
use App\Models\BatchHistoricalDataWork;
use App\Mail\BatchInventories;
use App\Models\BatchSharingForecast;
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
            $this->HistoricalSeriesGeneration();
            $this->HistoricalSeriesAnalysis();
            $this->GenerationForecast();
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
                                            //---------------------------------------
                                            // Attenzione aggiungere il modello di previsione se initial è uguale a forecast
                                            //---------------------------------------
                                            if ($work->initial=="new"){
                                                $accessdate=date("Y-m-d");
                                                $date_booking = strtotime('+13 month',strtotime($accessdate));
                                                $date_booking = date ('Y-m-1', $date_booking);
                                                //book operazione di analisi dati storici
                                                BatchHistoricalDataAnalysis::create(
                                                    [
                                                        'CompanyDataAnalysis' => $work->company_batch_inventory,
                                                        'productDataAnalysis' => $id,
                                                        'booking_historical_data_analysi' => $date_booking
                                                    ]);
                                                $catalog = $this->FindCatalog($work->company_batch_inventory,$data['0']);
                                                if ($catalog!=='0'){
                                                    //Memorizziamo nella tabella 'sales_lists' e 'historical data' il mese iniziale
                                                    $find_catalog = $this->TakeIdSalesList($catalog,$work->company_batch_inventory,$data['0']);
                                                    $initial_month = substr($accessdate,5,2);
                                                    DB::table('sales_lists')->where($catalog,$find_catalog)->where('company_sales_list',$work->company_batch_inventory)->update(
                                                        [
                                                            'initial_month_sales' => $initial_month,
                                                            'forecast_model' => '00'
                                                        ]
                                                    );
                                                    DB::table('historical_datas')->where('product_historical_data',$id)->where('company_historical_data',$work->company_batch_inventory)->update(
                                                            [
                                                                'initial_month' => $initial_month
                                                            ]
                                                    );
                                                }

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
                             $accessdate = date("Y-m-d");
                             $date_booking = strtotime('+13 month', strtotime($accessdate));
                             $date_booking = date('Y-m-1', $date_booking);
                             //book operazione di analisi dati storici
                             BatchHistoricalDataAnalysis::create(
                                 [
                                     'CompanyDataAnalysis' => $work->CompanyDataAnalysis,
                                     'productDataAnalysis' => $item->product_historical_data,
                                     'booking_historical_data_analysi' => $date_booking
                                 ]);
                             $initial_month = substr($accessdate, 5, 2);
                                 DB::table('sales_lists')->where('id_sales_list', $item->product_historical_data)->where('company_sales_list', $work->CompanyDataAnalysis)->update(
                                     [
                                         'initial_month_sales' => $initial_month,
                                         'forecast_model' => '00'
                                     ]
                                 );
                                 DB::table('historical_datas')->where('product_historical_data',$item->product_historical_data)->where('company_historical_data', $work->CompanyDataAnalysis)->update(
                                   [
                                       'initial_month' => $initial_month,
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
                        $initial = DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->select('initial_month_sales')->first();
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
                              'initial_month_holt' => $initial->initial_month_sales,
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
                        $regression = $this->Regression($totali,4);
                        $seasonal_factors = $this->GenerateSeasonalFactors($regression['trend'],$regression['level'],$totali,4);
                        DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->update(
                            [
                                'forecast_model' => '01',
                                'initial_month_sales' => $work->index_forecast
                            ]
                        );
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 9, $seasonal_factors[1],'factor1_mean_square_winter4');
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 10, $seasonal_factors[2],'factor2_mean_square_winter4');
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 11, $seasonal_factors[3],'factor3_mean_square_winter4');
                        $this->CreateMeanSquareWinter4Error($data->product_historical_data, $regression, 12, $seasonal_factors[4],'factor4_mean_square_winter4');
                        $forecast = $this->DevelopsForecast(1,4,$regression['level'],$regression['trend'],$seasonal_factors);
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
                              '1' => $forecast[1],
                              '2' => $forecast[2],
                              '3' => $forecast[3],
                              '4' => $forecast[4],
                              '5' => $forecast[1],
                              '6' => $forecast[2],
                              '7' => $forecast[3],
                              '8' => $forecast[4],
                          ]
                        );
                        if ($create){
                            $accessdate=date("Y-m-d");
                            $initial_date=date("Y-$initial->initial_month_sales-d");
                            $date_booking = strtotime('+3 month',strtotime($initial_date));
                            $accessdate = strtotime($accessdate);
                            while ($accessdate>=$date_booking) {
                                $date_booking = strtotime('+3 month',$date_booking);
                            }
                            $date_booking = date ('Y-m-1', $date_booking);
                        }

                    }

                   if($work->GenerationForecastModel=='10'){
                       $totali = $this->TotalPeriods($dati,2,$work->index_forecast);
                        //Se il modello è di Winter semestrale
                       $regression = $this->Regression($totali,2);
                       $seasonal_factors = $this->GenerateSeasonalFactors($regression['trend'],$regression['level'],$totali,2);
                       DB::table('sales_lists')->where('id_sales_list',$data->product_historical_data)->update(
                           [
                               'forecast_model' => '01',
                               'initial_month_sales' => $work->index_forecast
                           ]
                       );
                       $this->CreateMeanSquareWinter2Error($data->product_historical_data, $regression, 5, $seasonal_factors[1],'factor1_mean_square_winter2');
                       $this->CreateMeanSquareWinter2Error($data->product_historical_data, $regression, 6, $seasonal_factors[2],'factor2_mean_square_winter2');
                       $forecast = $this->DevelopsForecast(1,2,$regression['level'],$regression['trend'],$seasonal_factors);
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
                               '1' => $forecast[1],
                               '2' => $forecast[2],
                               '3' => $forecast[1],
                               '4' => $forecast[2],
                           ]
                       );
                       if ($create){
                           $accessdate=date("Y-m-d");
                           $initial_date=date("Y-$initial->initial_month_sales-d");
                           $date_booking = strtotime('+6 month',strtotime($initial_date));
                           $accessdate = strtotime($accessdate);
                           while ($accessdate>=$date_booking) {
                               $date_booking = strtotime('+6 month',$date_booking);
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


                        //Modifica dello stato dell'operazione di generazione previsione in eseguita
                        DB::table('batch_generation_forecasts')->where('id_generation_forecast',$work->id_generation_forecast)->update(
                          [
                              'executed_generation_forecast' => '1'
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
            $factor [$i+1] = $sell[$i+1] / (($trend*($i+1))+$level);
            else $factor [$i+1]=0;
        }
        return $factor;
    }

    public function DevelopsForecast($k,$n,$level,$trend,$factors){
        if ($factors!=null){
            if ($n==2 and $k==3) $k=1;
            if ($n==2 and $k==4) $k=2;
            if ($n==4 and $k==5) $k=1;
            if ($n==4 and $k==6) $k=2;
            if ($n==4 and $k==7) $k=3;
            if ($n==4 and $k==8) $k=4;
        }
        for ($i=$k;$i<($n+$k);$i++){
            if($factors==null){
                if($i<=12) $forecast[$i]=($trend * $i) + $level;
                else $forecast[$i-12]=($trend * ($i-12)) + $level;
            } else{
                if($i>$n) $forecast[$i]=(($trend * $i) + $level) * $factors[$i-$n];
                else $forecast[$i]=(($trend * $i) + $level) * $factors[$i];
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
}

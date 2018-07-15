<?php

namespace App\Http\Controllers;

use App\BatchHistoricalDataAnalysis;
use App\Mail\BatchHistoricalData;
use App\Models\BatchHistoricalDataWork;
use App\Mail\BatchInventories;
use App\Models\HistoricalData;
use App\Models\Inventory;
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
            dd('dati storici analizzati');
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
                            $catalog = $this->FindCatalog($work->company_batchHisDat,$sell->cod_inventory);
                            if ($catalog!=='0'){
                                //Creiamo e memorizziamo nella tabella 'sales_lists' e 'historical data' il mese iniziale
                                DB::table('sales_lists')->where('company_sales_list',$work->company_batchHisDat)->update(
                                    [
                                        'initial_month_sales' => $initial_month
                                    ]
                                );
                                DB::table('historical_datas')->where('company_historical_data',$work->company_batchHisDat)->update(
                                    [
                                        'initial_month' => $initial_month
                                    ]
                                );
                            }
                            $this->Mp=$initial_month;
                            $this->Md=1;
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
                    //Prenotazione operazione analisi serie storica
                    $accessdate=date("Y-m-d");
                    BatchHistoricalDataAnalysis::create(
                      [
                          'HistoricalDataAnalysis' => $work->company_batchHisDat,
                          'booking_historical_data_analysi' => $accessdate
                      ]
                    );
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
            $value = DB::table('historical_datas')->where('company_historical_data',$company)->where('product_historical_data',$id->id_sales_list)->select($mese)->first();
            foreach ($value as $val){
                    $add_value=$val+$quantity;
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $add = DB::table('historical_datas')->where('company_historical_data',$company)->where('product_historical_data',$id->id_sales_list)->update(
                    [
                        $mese => $add_value
                    ]);
            if ($add) return $add->HistoricalDataAnalysis;
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
                $data = DB::table('historical_datas')->where('company_historical_data',$work->HistoricalDataAnalysis)->get();
                if ($data){
                     foreach ($data as $item){
                         $tot_sell=0;
                         $i=0;
                         while ($i<12){
                             $i++;
                             $add = DB::table('historical_datas')->where('product_historical_data',$item->product_historical_data)->where('company_historical_data',$work->HistoricalDataAnalysis)->select($i)->first();
                             foreach ($add as $val){
                                 $tot_sell=$tot_sell+$val;
                             }
                         }
                         dd($tot_sell);

                     }
                }
            } return true;
        } else return false;
    }
}

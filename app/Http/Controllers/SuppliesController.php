<?php

namespace App\Http\Controllers;

use App\BatchHistoricalDataAnalysis;
use App\Http\Requests\Supplies\NewProvider;
use App\Http\Requests\Supplies\SettingConfig;
use App\Http\Requests\Supplies\UploadExpires;
use App\Http\Requests\Supplies\UploadInventory;
use App\Http\Requests\Supplies\UploadMapProvider;
use App\Models\Batch_Expiry;
use App\Models\Batch_inventory;
use App\Models\Batch_MappingInventoryProvider;
use App\Models\Batch_monitoringOrder;
use App\Models\BatchHistoricalData;
use App\Models\ConfigOrder;
use App\Models\Expiry;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF;
use App\Mail\BatchProduction;
use App\Mail\MonitoringExpire;
use App\Mail\SharingForecast;
use App\Mail\PurchaseOrderCanceled;
use App\Mail\PurchaseOrderReceived;
use App\Mail\PurchaseOrderTransmission;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderContent;
use Illuminate\Support\Facades\Mail;


class SuppliesController extends Controller
{
    public function ViewProvider(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $providers = Provider::select('*')->where('company_provider',$company_provider->company_employee)->orderby('rag_soc_provider')->paginate(env('PAGINATE_COMPANY'));
            if (count($providers)==0) session()->flash('message', 'Non hai caricato fornitori');
            $dato = $this->dataProfile();
            return view('supplies.view-providers',
                [
                    'dati' => $dato[0],
                    'company' =>$providers,

                ]);
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }

    function monthForecast($initial,$month){
        if ($initial==$month) return 1;
        if ($initial<$month) return $month - ($initial - 1);
        if ($initial>$month) return 13 -($initial - $month);
    }

    public function deleteProvider($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $query = Provider::select('supply_provider')->where('company_provider',$company_provider->company_employee)->where('id_provider',$id)->first();
            if (count($query)>0){
                if($query->supply_provider=='0'){
                    $del = Provider::whereKey($id)->delete();
                    if ($del) DB::table('mapping_inventory_providers')->where('company_mapping_provider',$company_provider->company_employee)->where('provider_mapping_provider',$id)->delete();
                    $messaggio = $del ? 'Il fornitore è stato cancellato' : 'Problemi con il Server riprovare l\'operazione';
                    session()->flash('message', $messaggio);
                    return redirect(route('providers'));
                } else {
                    session()->flash('message', 'Per eliminare questo fornitore, devi prima eliminare l\'aggregazione con la tua rete Supply Chain');
                    return redirect(route('providers'));
                }
            } else {
                session()->flash('message', 'Questo fornitore non è presente nel database');
                return redirect(route('providers'));
            }
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }

    public function addProvider(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
            return view('supplies.addprovider',
                [
                    'dati' => $dato[0],
                    'employee' => $employee[0]
                ]);
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }



    public function addNewProvider(NewProvider $request){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $request->all();
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $control_code = DB::table('providers')->where('company_provider',$company_provider->company_employee)->where('provider_cod',$dato['provider_cod'])->first();
            if(count($control_code)==0) {
                $messaggio = null;
                $control_piva = DB::table('company_offices')->where('partita_iva_company',$dato['iva_provider'])->first();
                if (count($control_piva)>0) $messaggio = 'Osserva che la sua partita iva è già presente nel nostro database, puoi trasmettere una richiesta di aggregazione Supply Chain a questa partita iva. ';
                $control_piva2 = DB::table('providers')->where('company_provider',$company_provider->company_employee)->where('iva_provider',$dato['iva_provider'])->first();
                if (count($control_piva2)>0) $messaggio = $messaggio.'La partita iva era già presente fra i tuoi fornitori. Controlla se hai commesso un\'errore. ';
                $create = Provider::create(
                    [
                        'company_provider' => $company_provider->company_employee,
                        'provider_cod' => $dato['provider_cod'],
                        'rag_soc_provider' => $dato['rag_soc_provider'],
                        'iva_provider' => $dato['iva_provider'],
                        'address_provider' => $dato['address_provider'],
                        'telefono_provider' => $dato['telefono_provider'],
                        'email_provider' => $dato['email_provider'],
                    ]
                );
                if ($create) $messaggio = $messaggio.' Il fornitore è stato inserito con successo.';
                session()->flash('message', $messaggio);
                return redirect()->route('providers');
            } else {
                session()->flash('message', 'Hai già utilizzato questo codice per un\'altro fornitore');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }

    public function upProvider($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            foreach ($dato as $datum) $company = $datum['id_company_office'];
            $provider = DB::table('providers')->where('company_provider',$company)->where('id_provider',$id)->first();
            if ($provider){
                $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
                return view('supplies.updateprovider',
                    [
                        'dati' => $dato[0],
                        'employee' => $employee[0],
                        'provider' => $provider
                    ]);
            } else {
                session()->flash('message', 'L\'azienda inserita non fa parte dei tuoi fornitori');
                return redirect()->route('employee');
            }
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }

    public function updateProvider(NewProvider $request){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $request->all();
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $control_code = DB::table('providers')->where('company_provider',$company_provider->company_employee)->where('provider_cod',$dato['provider_cod'])->where('id_provider','<>',$dato['id_provider'])->first();
            if($control_code==null) {
                $messaggio = null;
                $control_piva = DB::table('company_offices')->where('partita_iva_company',$dato['iva_provider'])->first();
                if (count($control_piva)>0) $messaggio = 'Osserva che la sua partita iva è già presente nel nostro database, puoi trasmettere una richiesta di aggregazione Supply Chain a questa partita iva. ';
                $control_piva2 = DB::table('providers')->where('company_provider',$company_provider->company_employee)->where('iva_provider',$dato['iva_provider'])->where('id_provider','<>',$dato['id_provider'])->first();
                if (count($control_piva2)>0) $messaggio = $messaggio.'La partita iva era già presente fra i tuoi fornitori. Controlla se hai commesso un\'errore. ';
                $up = DB::table('providers')->where('id_provider','=',$dato['id_provider'])->update(
                    [
                        'provider_cod' => $dato['provider_cod'],
                        'rag_soc_provider' => $dato['rag_soc_provider'],
                        'iva_provider' => $dato['iva_provider'],
                        'address_provider' => $dato['address_provider'],
                        'telefono_provider' => $dato['telefono_provider'],
                        'email_provider' => $dato['email_provider'],
                    ]
                );
                if($up) $messaggio = $messaggio.'I dati del fornitore sono stati aggiornati con successo.';
                session()->flash('message', $messaggio);
                return redirect()->route('providers');
            } else {
                session()->flash('message', 'Hai già utilizzato questo codice per un\'altro fornitore');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }

    public function ViewInventories(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $inventories = DB::table('inventories')->select('*')->where('company_inventory',$company_provider->company_employee)->orderby('cod_inventory')->paginate(env('PAGINATE_ITEM'));
            $dato = $this->dataProfile();
            return view('supplies.view-inventories',
                [
                    'dati' => $dato[0],
                    'item' =>$inventories,

                ]);
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }

    public function deleteInventories(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $del = DB::table('inventories')->where('company_inventory',$company_provider->company_employee)->delete();
            DB::table('sales_lists')->where('company_sales_list',$company_provider->company_employee)->where('inventory_sales_list','>',0)->delete();
            DB::table('batch_historical_datas')->where('company_batchHisDat',$company_provider->company_employee);
            DB::table('batch_historical_data_analyses')->where('CompanyDataAnalysis',$company_provider->company_employee)->delete();
            DB::table('batch_inventories')->where('company_batch_inventory',$company_provider->company_employee)->delete();
            $messaggio = $del ? 'L\'inventario è stato eliminato' : 'Problemi con il server riprova pià tardi';
            session()->flash('message', $messaggio);
            return redirect()->route('inventories');
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('employee');
        }
    }

    public function addInventories(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $iva =  DB::table('iva')->select('*')->get();
            $dato = $this->dataProfile();
            return view('supplies.add-inventories',
                [
                    'dati' => $dato[0],
                    'iva' =>$iva,
                ]);
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('inventories');
        }
    }

    public function storeInventories(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $block_historical = DB::table('batch_inventories')->where('initial','historical')->where('executed_batch_inventory','1')->where('company_batch_inventory',$company_provider->company_employee)->first();
            if (count($block_historical)>0) $block = 1; else $block = 0;
            $items = DB::table('inventories')->where('company_inventory', $company_provider->company_employee)->where('sale_inventory','=','1')->select('id_inventory','cod_inventory','title_inventory')->get();
            return view('supplies.store-inventories',
                [
                    'dati' => $dato[0],
                    'items' => $items,
                    'block' => $block,
                ]);
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('inventories');
        }
    }

    public function uploadInventories(UploadInventory $request){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $file = $request->file('inventory');
            if ($request->file('file-historical')){
                if($request->file('file-historical')->isValid() and $request->file('file-historical')->extension()=='txt'){
                    $file_historical = $request->file('file-historical');
                } else {
                    session()->flash('message', 'Non hai caricato un file valido');
                    return redirect()->route('employee');
                }
            }
            if($file->isValid() and $file->extension()=='txt'){
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $company_provider = Employee::where('user_employee',Auth::id())->join('company_offices','id_company_office','=','company_employee')->select('company_employee','rag_soc_company','email_company')->first();
                $access =  date('d-m-Y g-i-s');
                $filename = 'inventory_' . $company_provider->company_employee.'-'.$company_provider->rag_soc_company .$access. '.' . $file->extension();
                if (isset($file_historical) and $request->initial=="historical"){
                    $filename_historical = 'historical_' . $company_provider->company_employee.'-'.$company_provider->rag_soc_company . '.' . $file->extension();
                    $data['initial'] = 'historical';
                }
                if (!isset($data['initial'])){
                    if($request->initial=="forecast" or $request->initial=="new"){
                        $data['initial']=$request->initial;
                        if ($data['initial']=="forecast"){
                           $query = DB::table('inventories')->where('id_inventory',$request->id_inventory)->first();
                           if (count($query)==0){
                               session()->flash('message', 'Il prodotto selezionato per l\'inizializzazione della previsione non esiste');
                               return redirect()->route('employee');
                           } else $data['initial']=$request->id_inventory;
                        }
                    } else {
                        session()->flash('message', 'Operazione non riuscita riprova');
                        return redirect()->route('employee');
                    }
                }
                if (!isset($data['initial'])){
                    session()->flash('message', 'Operazione non riuscita riprova');
                    return redirect()->route('employee');
                } else {
                    $block = '0';
                    if(isset($filename_historical) and $filename_historical==true){
                        $file_historical->storeAs(env('CSV_INVENTORY'), $filename_historical);
                        $book = BatchHistoricalData::create(
                          [
                              'company_batchHisDat' => $company_provider->company_employee,
                              'url_batchHisDat' => env('CSV_INVENTORY') . '/' . $filename_historical,
                              'email_batchHisDat' => $company_provider->email_company,
                          ]
                        );
                        if ($book==false) $block = '1';
                    }
                    if ($block=='0'){
                        $file->storeAs(env('CSV_INVENTORY'), $filename);
                        $booking = Batch_inventory::create(
                          [
                              'company_batch_inventory' => $company_provider->company_employee,
                              'url_file_batch_inventory' => env('CSV_INVENTORY') . '/' . $filename,
                              'email_batch_inventory' => $company_provider->email_company,
                              'initial' => $data['initial']
                          ]
                        );
                        $messaggio = $booking ? 'Prenotazione riuscita riceverai un\'email quando l\'operazione sarà completata' : 'Problemi con il Server riprova di nuovo';
                        session()->flash('message', $messaggio);
                        return redirect()->route('employee');
                    } else {
                        session()->flash('message', 'Operazione non riuscita riprova');
                        return redirect()->route('employee');
                    }
                }
            } else {
                session()->flash('message', 'Non hai caricato un file valido');
                return redirect()->route('employee');
            }
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('inventories');
        }
    }

    public function downloadFile(){
        $path = "download/products_import.csv";
        return response()->download($path);
    }

    public function downloadHistorical(){
        $path = "download/historical_data.csv";
        return response()->download($path);
    }

    public function downloadExpires(){
        $path = "download/expires_data.csv";
        return response()->download($path);
    }

    public function downloadMapping(){
        $path = "download/mappingprovider.csv";
        return response()->download($path);
    }

    public function supplieControl(){
        $controllo = $this->block();
        if ($controllo){
            return view('errors.500');
        } else {
            $acquisti = Employee::where('user_employee','=',Auth::id())->select('acquisti','company_employee')->first();
            if($acquisti->acquisti=='1'){
                return $acquisti;
            }
            else {
                session()->flash('message', 'Non puoi accedere a queste informazioni');
                return redirect()->route('employee');
            }
        }
    }

    public function dataProfile(): \Illuminate\Support\Collection
    {
        $data = User::join('employees', 'id', '=', 'user_employee')->join('company_offices', 'employees.company_employee', '=', 'company_offices.id_company_office')->join('business_profiles', 'company_offices.id_admin_company', '=', 'business_profiles.id_admin')->join('comuni', 'comuni.id_comune', '=', 'company_offices.cap_company')->leftJoin('company_offices_extra_italia', 'company_offices_extra_italia.company_office', '=', 'company_offices.id_company_office')->where('id', Auth::id())->select('name', 'cognome', 'img_employee', 'responsabile', 'acquisti', 'produzione', 'vendite', 'rag_soc_company', 'cap_company', 'indirizzo_company', 'civico_company', 'logo', 'cap', 'comune', 'sigla_prov', 'cap_company_office_extra', 'city_company_office_extra', 'state_company_office_extra', 'nazione_company','id_company_office','provincia','visible_user','visible_business')->get();
        return $data;
    }

    public function block(){
        $profilo = Auth::user()->profile;
        $business = Auth::user()->business;
        $admin = Auth::user()->admin;
        if ($profilo=='1' and $business=='1' and $admin=='0') {
            return false;
        }
        else return true;
    }

    public function ViewExpires(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $expires = DB::table('inventories')->select('*')->where('company_inventory',$company_provider->company_employee)->where('expire_inventory','1')->orderby('cod_inventory')->paginate(env('PAGINATE_ITEM'));
            $dato = $this->dataProfile();
            return view('supplies.view-expires',
                [
                    'dati' => $dato[0],
                    'item' =>$expires,

                ]);
        } else {
            session()->flash('message', 'Non puoi accedere a queste informazioni');
            return redirect()->route('employee');
        }
    }

    public function addExpires(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            return view('supplies.add-expires',
                [
                    'dati' => $dato[0],
                ]);
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('expires');
        }
    }

    public function deleteExpires(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $up1 = DB::table('inventories')->where('company_inventory',$company_provider->company_employee)->update(
              [
                  'expire_inventory' => '0'
              ]
            );
            $up2 = DB::table('expiries')->where('company_expiry',$company_provider->company_employee)->delete();
            if($up1 and $up2) $messaggio = 'Operazione eseguita con successo'; else $messaggio = 'Problemi con il Server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('expires');
        } else {
            session()->flash('message', 'Non puoi effettuare questa operazione');
            return redirect()->route('employee');
        }
    }

    public function upExpires($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            $company_provider = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $item = DB::table('inventories')->where('id_inventory',$id)->select('cod_inventory','title_inventory','category_first','category_second','unit_inventory','stock','url_inventory','brand','ean_inventory')->first();
            if ($item){
                $expire = DB::table('expiries')->where('company_expiry',$company_provider->company_employee)->where('inventory_expiry',$id)->select('stock_expiry','date_expiry')->get();
                if (count($expire)>0){
                    return view('supplies.item-expires',
                        [
                            'dati' => $dato[0],
                            'item' => $item,
                            'expire' => $expire,
                        ]);
                } else {
                    session()->flash('message', 'Il prodotto non ha date di scadenza memorizzate');
                    return redirect()->route('expires');
                }
            } else{
                session()->flash('message', 'Non abbiamo trovato il prodotto richiesto');
                return redirect()->route('expires');
            }
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('expires');
        }
    }

    public function delExpires($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $company_provider = Employee::where('user_employee', Auth::id())->select('company_employee')->first();
            $check = DB::table('inventories')->where('id_inventory',$id)->where('company_inventory',$company_provider->company_employee)->select('*')->first();
            if (count($check)>0){
                DB::table('expiries')->where('company_expiry',$company_provider->company_employee)->where('inventory_expiry',$id)->delete();
                $del = DB::table('inventories')->where('id_inventory',$id)->update(
                    [
                        'expire_inventory' => '0'
                    ]
                );
                return $del;
            }
        } else {
            session()->flash('message', 'Non puoi effettuare questa operazione');
            return redirect()->route('employee');
        }
    }

    public function storeExpires(){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            return view('supplies.store-expires',
                [
                    'dati' => $dato[0],
                ]);
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('expires');
        }
    }

    public function uploadExpires(UploadExpires $request){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $file = $request->file('expires');
            if($file->isValid() and $file->extension()=='txt'){
                $company_provider = Employee::where('user_employee',Auth::id())->join('company_offices','id_company_office','=','company_employee')->select('company_employee','rag_soc_company','email_company')->first();
                $access =  date('d-m-Y g-i-s');
                $filename = 'expires_' . $company_provider->company_employee.'-'.$company_provider->rag_soc_company.$access. '.' . $file->extension();
                $file->storeAs(env('CSV_EXPIRES'), $filename);
                $booking = Batch_Expiry::create(
                            [
                                'company_batch_expiries' => $company_provider->company_employee,
                                'url_file_batch_expiries' => env('CSV_EXPIRES') . '/' . $filename,
                                'email_batch_expiries' => $company_provider->email_company,
                            ]
                        );
                $messaggio = $booking ? 'Prenotazione riuscita riceverai un\'email quando l\'operazione sarà completata' : 'Problemi con il Server riprova di nuovo';
                session()->flash('message', $messaggio);
                return redirect()->route('employee');
            } else {
                session()->flash('message', 'Non hai caricato un file valido');
                return redirect()->route('employee');
            }
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('expires');
        }
    }

    public function mappingProviders($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            foreach ($dato as $dati) $id_company = $dati->id_company_office;
            $providers = DB::table('providers')->where('company_provider',$id_company)->where('id_provider',$id)->first();
            if ($providers->supply_provider==1){
                $ean = DB::table('supply_chains')->where('company_supply_shares',$id_company)->where('company_supply_received',$providers->provider_supply)->select('ean_mapping')->first();
                if ($ean->ean_mapping=='1'){
                    session()->flash('message', 'La mappatura dei prodotti con questo fornitore avviene tramite codice a barre');
                    return redirect()->route('providers');
                }
            }
            $mapping = DB::table('mapping_inventory_providers')->join('inventories','id_inventory','=','inventory_mapping_provider')->where('company_mapping_provider',$id_company)->where('provider_mapping_provider',$id)->select('cod_inventory','cod_mapping_inventory_provider','url_inventory','title_inventory','price_provider','unit_inventory','id_mapping_inventory_provider')->paginate(env('PAGINATE_ITEM'));
            return view('supplies.view-mapping',
                    [
                        'dati' => $dato[0],
                        'providers' => $providers,
                        'mapping' => $mapping,

                    ]);
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('expires');
        }
    }

    public function addMapping($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            foreach ($dato as $dati) $id_company = $dati->id_company_office;
            $provider = DB::table('providers')->where('company_provider',$id_company)->where('id_provider',$id)->first();
            return view('supplies.add-mapping',
                [
                    'dati' => $dato[0],
                    'providers' => $provider
                ]);
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('expires');
        }
    }

    public function deleteMapping($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $company_provider = Employee::where('user_employee', Auth::id())->select('company_employee')->first();
            $del = DB::table('mapping_inventory_providers')->where('company_mapping_provider',$company_provider->company_employee)->where('provider_mapping_provider',$id)->delete();
            $messaggio = $del ? 'Il mapping è stato eliminato' : 'Problemi con il server riprova';
            session()->flash('message', $messaggio);
            return redirect()->route('mapping-providers',$id);
        } else {
            session()->flash('message', 'Non puoi effettuare questa operazione');
            return redirect()->route('employee');
        }
    }

    public function delMapping($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $company_provider = Employee::where('user_employee', Auth::id())->select('company_employee')->first();
            $del = DB::table('mapping_inventory_providers')->where('id_mapping_inventory_provider',$id)->where('company_mapping_provider',$company_provider->company_employee)->delete();
            return $del;
        } else {
            session()->flash('message', 'Non puoi effettuare questa operazione');
            return redirect()->route('employee');
        }
    }



    public function storeMapping($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $dato = $this->dataProfile();
            foreach ($dato as $dati) $id_company = $dati->id_company_office;
            $provider = DB::table('providers')->where('company_provider',$id_company)->where('id_provider',$id)->first();
            if (count($provider)>0){
                return view('supplies.store-mapping',
                    [
                        'dati' => $dato[0],
                        'providers' => $provider
                    ]);
            } else {
                session()->flash('message', 'Il fornitore non è presente nella tua lista');
                return redirect()->route('providers');
            }
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('providers');
        }
    }

    public function uploadMapping(UploadMapProvider $request){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $id= $request->id;
            $company_provider = Employee::where('user_employee',Auth::id())->join('company_offices','id_company_office','=','company_employee')->select('company_employee','rag_soc_company','email_company')->first();
            $find = DB::table('providers')->where('company_provider',$company_provider->company_employee)->where('id_provider',$id)->first();
            if (count($find)>0){
                $file = $request->file('mapping');
                if($file->isValid() and $file->extension()=='txt'){
                    $access =  date('d-m-Y g-i-s');
                    $filename = 'map-provider_' . $company_provider->company_employee.'-'.$company_provider->rag_soc_company .'-'.$find->provider_supply .$access.'.' . $file->extension();
                    $file->storeAs(env('CSV_MAP_PROVIDER'), $filename);
                    $booking = Batch_MappingInventoryProvider::create(
                        [
                            'company_batchMapPro' => $company_provider->company_employee,
                            'url_file_batch_mapping_provider' => env('CSV_MAP_PROVIDER') . '/' . $filename,
                            'email_batch_mapping_provider' => $company_provider->email_company,
                            'provider_batchMapPro' => $id
                        ]
                    );
                    $messaggio = $booking ? 'Prenotazione riuscita riceverai un\'email quando l\'operazione sarà completata' : 'Problemi con il Server riprova di nuovo';
                    session()->flash('message', $messaggio);
                    return redirect()->route('providers');
                } else {
                    session()->flash('message', 'Non hai caricato un file valido');
                    return redirect()->route('store_mapping',$id);
                }
            } else {
                session()->flash('message', 'Il fornitore non è presente nella tua lista');
                return redirect()->route('providers');
            }
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('providers');
        }
    }

    public function configOrder($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $company_provider = Employee::where('user_employee',Auth::id())->join('company_offices','id_company_office','=','company_employee')->select('company_employee','rag_soc_company','email_company')->first();
            $check = DB::table('providers')->where('id_provider',$id)->where('company_provider',$company_provider->company_employee)->select('*')->first();
            if($check){
                $config = DB::table('config_orders')->where('company_config_order',$company_provider->company_employee)->where('provider_config_order',$id)->first();
                if($config==null){
                 $config = ConfigOrder::create(
                   [
                       'company_config_order' => $company_provider->company_employee,
                       'provider_config_order' => $id
                   ]
                 );
                }
                $provider = Provider::select('*')->where('company_provider',$company_provider->company_employee)->where('id_provider',$id)->select('rag_soc_provider','address_provider','telefono_provider','email_provider')->first();
                $data = $this->dataProfile();
                $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email','id_employee')->first();
                return view('supplies.config-order',[
                    'dati' => $data[0],
                    'employee' => $employee,
                    'provider' => $provider,
                    'config' => $config
                ]);
            } else {
                session()->flash('message', 'Questo fornitore non è presente nella tua lista');
                return redirect()->route('providers');
            }
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('providers');
        }
    }

    public function settingConfig(SettingConfig $request){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
                $data = $request->all();
                $company_provider = Employee::where('user_employee',Auth::id())->join('company_offices','id_company_office','=','company_employee')->select('company_employee')->first();
                $check = DB::table('config_orders')->where('company_config_order',$company_provider->company_employee)->where('provider_config_order',$data['provider_config_order'])->first();
                if($check){
                    if ($data['window_first_config']>$data['window_last_config']) {
                        session()->flash('message', 'Controlla la finestra temporale di esecuzione dell\'ordine');
                        return redirect()->route('config-order',$data['provider_config_order']);
                    } else {
                        if ($data['min_import_config']>$data['max_import_config']){
                            session()->flash('message', 'L\'importo minimo dell\'ordine non può superare l\'importo massimo');
                            return redirect()->route('config-order',$data['provider_config_order']);
                        } else {
                            if ($data['max_import_config']==0){
                                session()->flash('message', 'L\'importo massimo dell\'ordine non può essere uguale a zero');
                                return redirect()->route('config-order',$data['provider_config_order']);
                            } else {
                                if (!isset($data['level_config'])) $data['level_config']="0";
                                if ($data['transmission_config']!=="1") $data['transmission_config'] = "0";
                                if ($data['execute_config']!=="1"){
                                    $data['days_number_config']=0;
                                    $data['execute_config']='0';
                                }
                                $up = DB::table('config_orders')->where('company_config_order',$company_provider->company_employee)->where('provider_config_order',$data['provider_config_order'])->update(
                                  [
                                      'lead_time_config' => $data['lead_time_config'],
                                      'window_first_config' => $data['window_first_config'],
                                      'window_last_config' => $data['window_last_config'],
                                      'min_import_config' => $data['min_import_config'],
                                      'max_import_config' => $data['max_import_config'],
                                      'mapping_config' => $data['mapping_config'],
                                      'transmission_config' => $data['transmission_config'],
                                      'execute_config' => $data['execute_config'],
                                      'days_number_config' => $data['days_number_config'],
                                      'level_config' => $data['level_config'],
                                  ]
                                );
                                $date_booking = date('Y-m-d');
                                $limit_day = date('Y-m-'.$data['window_last_config']);
                                $first_day = date('Y-m-'.$data['window_first_config']);
                                if ($data['level_config']=='0'){
                                    if ($data['days_number_config']>0) {
                                        $date_booking = strtotime('+'.$data['days_number_config'].' days',strtotime($date_booking));
                                        $limit_day = date('Y-m-'.$data['window_last_config'],$date_booking);
                                        $first_day = date('Y-m-'.$data['window_first_config'],$date_booking);
                                        $date_booking = date ('Y-m-d', $date_booking);
                                    } else {
                                        $date_booking = strtotime('+1 month',strtotime($date_booking));
                                        $limit_day = date('Y-m-'.$data['window_last_config'],$date_booking);
                                        $first_day = date('Y-m-'.$data['window_first_config'],$date_booking);
                                        $date_booking = date ('Y-m-01', $date_booking);
                                    }
                                }
                                if ($data['transmission_config']=='1'){
                                    $email_provider = DB::table('providers')->where('id_provider',$data['provider_config_order'])->select('email_provider')->first();
                                    $email = $email_provider->email_provider;
                                } else $email = null;
                                $select = DB::table('batch_monitoring_orders')->where('company_batchMonOrder',$company_provider->company_employee)->where('configOrder_batchMonOrder',$check->id_config_order)->first();
                                if (count($select)>0){
                                    $batch = DB::table('batch_monitoring_orders')->where('company_batchMonOrder',$company_provider->company_employee)->where('configOrder_batchMonOrder',$check->id_config_order)->update(
                                        [
                                            'level_control' => $data['level_config'],
                                            'date_batch_monitoring_order' => $date_booking,
                                            'limit_day_batch_monitoring_order' => $limit_day,
                                            'first_day_batch_monitoring_order' => $first_day,
                                            'email_monitoring_order' => $email
                                        ]
                                    );
                                } else {
                                    $batch = Batch_monitoringOrder::create(
                                        [
                                            'company_batchMonOrder' => $company_provider->company_employee,
                                            'configOrder_batchMonOrder' => $check->id_config_order,
                                            'level_control' => $data['level_config'],
                                            'date_batch_monitoring_order' => $date_booking,
                                            'limit_day_batch_monitoring_order' => $limit_day,
                                            'first_day_batch_monitoring_order' => $first_day,
                                            'email_monitoring_order' => $email
                                        ]
                                    );
                                }
                                session()->flash('message', 'Le informazioni sono state aggiornate');
                                return redirect()->route('config-order',$data['provider_config_order']);
                            }
                        }
                    }
                } else {
                    session()->flash('message', 'Il fornitore non appartiene alla tua lista');
                    return redirect()->route('config-order',$data['provider_config_order']);
                }
        } else {
            session()->flash('message', 'Non puoi effettuare questa operazione');
            return redirect()->route('employee');
        }
    }

    public function generatedOrder($id){
        $acquisti = $this->supplieControl();
        if ($acquisti->acquisti=='1') {
            $company = Employee::where('user_employee',Auth::id())->join('company_offices','id_company_office','=','company_employee')->select('company_employee')->first();
            $check = DB::table('providers')->where('company_provider',$company->company_employee)->where('id_provider',$id)->first();
            if (count($check)==1){
                $work = DB::table('batch_monitoring_orders')->join('config_orders','id_config_order','=','configOrder_batchMonOrder')->where('company_batchMonOrder',$company->company_employee)->where('provider_config_order',$id)->select('*')->first();
                if (count($work)==1){
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

                        } else {
                            //Cancelliamo l'ordine
                            DB::table('purchase_orders')->where('id_purchase_order',$order->id_purchase_order)->delete();
                            session()->flash('message', 'La merce disponibile non rende necessario effetuare un ordine');
                            return redirect()->route('providers');
                        }
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
                    unlink($filename);
                    session()->flash('message', 'Ti abbiamo trasmesso via email l\'ordine generato');
                    return redirect()->route('providers');
                } else {
                    session()->flash('message', 'Non hai inserito la configurazione degli ordini');
                    return redirect()->route('config-order',$id);
                }
            } else {
                session()->flash('message', 'Il fornitore non è presente nella tua lista');
                return redirect()->route('providers');
            }
        } else {
            session()->flash('message', 'Non hai il privilegio per effettuare questa operazione');
            return redirect()->route('providers');
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
}

<?php

namespace App\Http\Controllers;

use App\BatchHistoricalDataAnalysis;
use App\Http\Requests\Supplies\NewProvider;
use App\Http\Requests\Supplies\UploadExpires;
use App\Http\Requests\Supplies\UploadInventory;
use App\Models\Batch_Expiry;
use App\Models\Batch_inventory;
use App\Models\BatchHistoricalData;
use App\Models\Expiry;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\User;
use Illuminate\Support\Facades\DB;


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
                $filename = 'inventory_' . $company_provider->company_employee.'-'.$company_provider->rag_soc_company . '.' . $file->extension();
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
                $filename = 'expires_' . $company_provider->company_employee.'-'.$company_provider->rag_soc_company . '.' . $file->extension();
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

    public function downloadMapping(){

    }

    public function storeMapping($id){

    }
}

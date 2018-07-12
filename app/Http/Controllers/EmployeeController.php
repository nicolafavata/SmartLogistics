<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\NewEmployee;
use App\Http\Requests\Employee\PictureUpdate;
use App\Http\Requests\Employee\ProfileUpdate;
use App\Http\Requests\Employee\UpdateCompany;
use App\Http\Requests\Employee\UpEmployee;
use App\Http\Requests\Employee\Visible;
use App\Http\Requests\Employee\Research;
use App\Mail\AggregationAccepted;
use App\Mail\AggregationCancel;
use App\Mail\AggregationRequest;
use App\Mail\DeleteSupplyChain;
use App\Mail\InformationSupply;
use App\Models\CompanyOffice;
use App\Models\CompanyOfficeExtraItalia;
use App\Models\Comune;
use App\Models\Employee;
use App\Models\Provider;
use App\Models\SupplyChain;
use App\Models\SupplyRequest;
use App\Models\VisibleComune;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\VerifyUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;


class EmployeeController extends Controller
{
    public function block(){
        $profilo = Auth::user()->profile;
        $business = Auth::user()->business;
        $admin = Auth::user()->admin;
        if ($profilo=='1' and $business=='1' and $admin=='0') {
            return false;
        }
        else return true;
    }

    public function index(){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $this->dataProfile();
            return view('employee.employee', [
                'dati' => $data[0],
            ]);
        }
    }

    public function newPassword(){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            return view('auth.passwords.email');
        }
    }

    public function picture(){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $this->dataProfile();
            return view('employee.picture', [
               'dati' => $data[0],
            ]);
        }
    }

    public function updatePicture(PictureUpdate $request){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            if ($request['img_employee']!=null){
                $file = $request->file('img_employee');
                if ($file->isValid()) {
                    $id_user = Auth::id();
                    $rag_soc = Employee::join('company_offices','id_company_office','=','company_employee')->where('user_employee','=',$id_user)->select('rag_soc_company','img_employee')->get();
                    foreach ($rag_soc as $rag) {
                        $filename = 'employee' . $id_user .'-company'. $rag['rag_soc_company'] . '.' . $file->extension();
                        $up = $file->storeAs(env('IMG_PROFILE'), $filename);
                        if ($rag['img_employee']=='0'){
                            $path = env('IMG_PROFILE') . '/' . $filename;
                            $up = Employee::where('user_employee','=',$id_user)->update(
                                [
                                    'img_employee' => $path,
                                ]
                            );
                        }
                        if ($up) {
                            $messaggio = 'La foto del profilo è stata aggiornata con successo';
                            session()->flash('message', $messaggio);
                            return redirect()->route('picture');
                        } else {
                            $messaggio = 'La foto del profilo non è stata aggiornata riprovare';
                            session()->flash('message', $messaggio);
                            return redirect()->route('picture');
                        }
                    }
                }
            }
        }
    }

    public function myProfile(){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $this->dataProfile();
            $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
            return view('employee.profile',[
               'dati' => $data[0],
               'employee' => $employee[0],
            ]);
        }
    }

    public function upProfile(){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $this->dataProfile();
            $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
            return view('employee.updateprofile',[
                'dati' => $data[0],
                'employee' => $employee[0],
            ]);
        }
    }

    public function updateMyProfile(ProfileUpdate $request){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $request->all();
            if (($data['email'])!=(Auth::user()->email)){
                $up = User::whereKey(Auth::id())->update(
                    [
                        'email' => $data['email'],
                    ]
                );
            }
            if (($data['tel_employee']!=null) or ($data['cell_employee']!=null)){
                $up = Employee::where('user_employee','=',Auth::id())->update(
                    [
                        'tel_employee' => $data['tel_employee'],
                        'cell_employee' => $data['cell_employee'],

                    ]
                );
            }
            if (isset($up) and ($up)){
                $messaggio = 'I dati sono stati aggiornati';

            } else {
                $messaggio = 'I dati inseriti non hanno comportato un aggiornamento';
            }
            session()->flash('message', $messaggio);
            return redirect()->route('my_profile');
        }
    }

    public function myCompany(){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $this->dataProfile();
            foreach ($data as $id){
                $company = CompanyOffice::whereKey($id['id_company_office'])->select('partita_iva_company','codice_fiscale_company','telefono_company','cellulare_company','fax_company','email_company')->get();
            }
            return view('employee.company',[
                'dati' => $data[0],
                'company' => $company[0],
            ]);
        }
    }

    public function upCompany(){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $this->dataProfile();
            foreach ($data as $dato){
                $company = CompanyOffice::whereKey($dato['id_company_office'])->select('partita_iva_company','codice_fiscale_company','telefono_company','cellulare_company','fax_company','email_company')->get();
                if ($dato['cap_company']=='8092'){
                    $nazioni = DB::table('stati')->select('nome_stati')->orderBy('nome_stati')->get();
                    $comuni = [
                        '1' => '1',
                    ];
                    $province =[
                        '1' =>'1',
                    ];
                } else {
                    $nazioni =[
                            '1' =>'1',
                    ];
                    $province = DB::table('comuni')->select('provincia')->groupBy('provincia')->orderBy('provincia')->get();
                    $comuni= DB::table('comuni')->select('comune','id_comune')->orderBy('comune')->get();
                }
            }
            return view('employee.updatecompany',[
                'dati' => $data[0],
                'company' => $company[0],
                'comune' => $comuni,
                'province' => $province,
                'nazioni' => $nazioni
            ]);
        }
    }

    public function updateMyCompany(UpdateCompany $request){
        $controllo = $this->block();
        if ($controllo==true){
            return view('errors.500');
        } else {
            $data = $request->all();
            $idstore = Employee::where('user_employee','=',Auth::id())->select('company_employee')->get();
            foreach ($idstore as $id) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $up = CompanyOffice::whereKey($id->company_employee)->update(
                    [
                        'rag_soc_company' => $data['rag_soc_company'],
                        'partita_iva_company' => $data['partita_iva_company'],
                        'codice_fiscale_company' => $data['codice_fiscale_company'],
                        'nazione_company' => $data['nazione_company'],
                        'cap_company' => $data['cap_company'],
                        'indirizzo_company' => $data['indirizzo_company'],
                        'civico_company' => $data['civico_company'],
                        'telefono_company' => $data['telefono_company'],
                        'cellulare_company' => $data['cellulare_company'],
                        'fax_company' => $data['fax_company'],
                        'email_company' => $data['email_company']
                    ]
                );
                if (($data['cap_company'] == '8092') and ($up == true)) {
                    CompanyOfficeExtraItalia::where('company_office', '=', $id->company_employee)->update(
                        [
                            'cap_company_office_extra' => $data['cap_company_office_extra'],
                            'city_company_office_extra' => $data['city_company_office_extra'],
                            'state_company_office_extra' => $data['state_company_office_extra']
                        ]
                    );
                }
                $messaggio = $up ? 'I dati sono stati aggiornati' : 'I dati non sono stati aggiornati riprovare';
                session()->flash('message', $messaggio);
                return redirect()->route('my_company');
            }

        }
    }

    public function addEmployee(){
        $controllo = $this->block();
        if ($controllo){
            return view('errors.500');
        } else {
            $responsabile = Employee::where('user_employee','=',Auth::id())->select('responsabile')->first();
            if($responsabile->responsabile=='1'){
                $dato = $this->dataProfile();
                $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
                return view('employee.addemployee',
                    [
                        'dati' => $dato[0],
                        'employee' => $employee[0]
                    ]);
            } else {
                $messaggio = 'Solo il responsabile della sede può aggiungere un impiegato';
                session()->flash('message', $messaggio);
                return redirect()->route('employee');
            }
        }
    }

    public function addNewEmployee(NewEmployee $request){
        $controllo = $this->block();
        if ($controllo){
            return view('errors.500');
        } else {
            $responsabile = Employee::where('user_employee','=',Auth::id())->select('responsabile')->first();
            if($responsabile->responsabile=='1'){
                $data = $request->all();
                //Registrazione utente
                $push = User::create([
                    'business'=> '1',
                    'name' => $data['name'],
                    'admin' => '0',
                    'cognome'=>$data['cognome'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password'])
                ]);
                if ($push) {
                    VerifyUser::create([
                        'user_id' => $push->id,
                        'token' => str_random(40),
                    ]);
                    Mail::to($push->email)->send(new VerifyMail($push));
                    $id_user=($push->id);

                    //Memorizziamo l'id della sede
                    $id = Employee::where('user_employee','=',Auth::id())->select('company_employee')->first();

                    if(!isset($data['acquisti'])) $data['acquisti']='0';
                    if(!isset($data['produzione'])) $data['produzione']='0';
                    if(!isset($data['vendite'])) $data['vendite']='0';
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    //Profilo impiegato
                    $profile = Employee::create(
                        [
                            'user_employee' => $id_user,
                            'matricola' => $data['matricola'],
                            'tel_employee'=> $data['tel_employee'],
                            'cell_employee' => $data['cell_employee'],
                            'company_employee' => $id->company_employee,
                            'responsabile' => '0',
                            'acquisti' => $data['acquisti'],
                            'produzione' => $data['produzione'],
                            'vendite' => $data['vendite']
                        ]
                    );
                    if ($profile){
                        User::where('id',$id_user)->update(
                            [
                                'profile' => '1'
                            ]
                        );
                        $messaggio = 'L\'impiegato è stato inserito con successo';
                        session()->flash('message', $messaggio);
                        return redirect()->route('viewemployees');
                    } else {
                        $messaggio = 'Problemi con il server riprova successivamente';
                        session()->flash('message', $messaggio);
                        return redirect()->route('employee');
                    }
                }
            } else {
                $messaggio = 'Solo il responsabile della sede può aggiungere un impiegato';
                session()->flash('message', $messaggio);
                return redirect()->route('employee');
            }
        }
    }

    public function viewEmployees(){
        $responsabile = $this->responsabileControl();
        if($responsabile->responsabile=='1'){
            $dato = $this->dataProfile();
            $employee = User::join('employees','id','user_employee')->where('id','<>',Auth::id())->where('company_employee','=',$responsabile->company_employee)->select('name','cognome','email','users.created_at','matricola','tel_employee','cell_employee','img_employee','responsabile','acquisti','produzione','vendite')->orderBy('cognome')->paginate(env('PAGINATE_EMPLOYEE'));
            return view('employee.view-employee',
                [
                    'dati' => $dato[0],
                    'employee' =>$employee

                ]);
        } else {
            return view('errors.500');
        }

    }

    public function upEmployees(){
        $responsabile = $this->responsabileControl();
        if($responsabile->responsabile=='1'){
            $dato = $this->dataProfile();
            $employee = User::join('employees','id','user_employee')->where('id','<>',Auth::id())->where('company_employee','=',$responsabile->company_employee)->select('name','cognome','email','users.created_at','matricola','tel_employee','cell_employee','img_employee','user_employee','acquisti','produzione','vendite','responsabile')->orderBy('cognome')->paginate(env('PAGINATE_EMPLOYEE'));
            return view('employee.up-employee',
                [
                    'dati' => $dato[0],
                    'employee' =>$employee

                ]);
        } else {
            return view('errors.500');
        }
    }

    public function updateEmployee(UpEmployee $request){
        $responsabile = $this->responsabileControl();
        if($responsabile->responsabile=='1'){
            $data=$request->all();
            $up = Employee::where('user_employee',$data['user_employee'])->select('company_employee')->first();
            if ($up['company_employee']==$responsabile->company_employee){
                    $up_user = User::whereKey($data['user_employee'])->update(
                        [
                            'name' => $data['name'],
                            'cognome' => $data['cognome']
                        ]
                    );
                    if (isset($up_user) and ($up_user==true)){

                        if(!isset($data['acquisti'])) $data['acquisti']='0';
                        if(!isset($data['produzione'])) $data['produzione']='0';
                        if(!isset($data['vendite'])) $data['vendite']='0';
                        if(!isset($data['responsabile'])) $data['responsabile']='0'; else {
                            $data['acquisti']='1';
                            $data['produzione']='1';
                            $data['vendite']='1';
                        }
                        $up_employee = Employee::where('user_employee',$data['user_employee'])->update(
                          [
                              'matricola' => $data['matricola'],
                              'tel_employee' => $data['tel_employee'],
                              'cell_employee' => $data['cell_employee'],
                              'responsabile' => $data['responsabile'],
                              'acquisti' => $data['acquisti'],
                              'produzione' => $data['produzione'],
                              'vendite' => $data['vendite']
                          ]
                        );
                        if (isset($up_employee) and ($up_employee==true)) {
                            $messaggio = 'Le informazioni sono state aggiornate';
                            session()->flash('message', $messaggio);
                            return redirect()->route('viewemployees');
                        } else {
                            $messaggio = 'Problemi con il server riprovare';
                            session()->flash('message', $messaggio);
                            return redirect()->route('upemployee');
                        }
                    } else {
                        $messaggio = 'Problemi con il server riprovare';
                        session()->flash('message', $messaggio);
                        return redirect()->route('upemployee');
                    }
            } else {
                $messaggio = 'Non hai i diritti per eseguire questa operazione';
                session()->flash('message', $messaggio);
                return redirect()->route('upemployee');
            }
        } else {
            return view('errors.500');
        }
    }

    public function delEmployees(){
        $responsabile = $this->responsabileControl();
        if($responsabile->responsabile=='1'){
            $dato = $this->dataProfile();
            $employee = User::join('employees','id','user_employee')->where('id','<>',Auth::id())->where('company_employee','=',$responsabile->company_employee)->select('name','cognome','email','users.created_at','matricola','tel_employee','cell_employee','img_employee','user_employee')->orderBy('cognome')->paginate(env('PAGINATE_EMPLOYEE'));
            return view('employee.del-employee',
                [
                    'dati' => $dato[0],
                    'employee' =>$employee

                ]);
        } else {
            return view('errors.500');
        }
    }

    public function delEmployee($id_employee){
        $responsabile = $this->responsabileControl();
        if($responsabile->responsabile=='1') {
            $del = Employee::where('user_employee',$id_employee)->select('company_employee')->first();
            if ($del->company_employee==$responsabile->company_employee){
                $delete = User::whereKey($id_employee)->delete();
                $messaggio = $delete ? 'Il dipendente è stato eliminato' : 'Problemi con il server riprovare';
                session()->flash('message', $messaggio);
                return redirect()->route('viewemployees');
            } else {
                return view('errors.500');
            }
        } else {
            return view('errors.500');
        }
    }

    public function visibleCompany(){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1'){
            $data = $this->dataProfile();
            $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
            foreach ($data as $dato) {
                if($dato->visible_user=='1'){
                   $visible = VisibleComune::join('comuni','cap_visible','id_comune')->where('company_office_visible',$dato->id_company_office)->select('id_comune','cap','comune','sigla_prov')->get();
                }
            }
            $comuni = Comune::select('id_comune','cap','comune')->where('sigla_prov',$dato->sigla_prov)->get();
            if (!isset($visible)){
                $visible = [
                    '1' => '1'
                ];
            }
            return view('employee.visible',[
                'dati' => $data[0],
                'employee' => $employee[0],
                'comuni' => $comuni,
                'visibili' => $visible,
            ]);


        } else {
            return view('errors.500');
        }
    }

    public function changeVisible(Visible $request){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $data = $request->all();
            if (isset($data['visible_user']) and ($data['visible_user']=='1') and (isset($data['cap_visible']))){
                VisibleComune::where('company_office_visible','=',$responsabile->company_employee)->delete();
                foreach ($data['cap_visible'] as $id){
                    VisibleComune::create(
                      [
                          'cap_visible' => $id,
                          'company_office_visible' => $responsabile->company_employee,
                      ]
                    );
                }
            }
            if (!isset($data['visible_user'])) VisibleComune::where('company_office_visible','=',$responsabile->company_employee)->delete();
            if (!isset($data['visible_user'])) $data['visible_user'] = '0';
            if (!isset($data['visible_business'])) $data['visible_business'] = '0';
            $up = CompanyOffice::whereKey($responsabile->company_employee)->update(
              [
                  'visible_user' => $data['visible_user'],
                  'visible_business' => $data['visible_business'],
              ]
            );
            $messaggio = $up ? 'Le informazini sulla visibilità sono state aggiornate' : 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('visiblecompany');
        } else  return view('errors.500');
    }

    public function researchCompany(){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $data = $this->dataProfile();
            $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
            return view('employee.research',[
                'dati' => $data[0],
                'employee' => $employee[0],
            ]);
        } else return view('errors.500');
    }

    public function findCompany(Research $request){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $data = $request->only('research');
            $dati = $this->dataProfile();
            $business = CompanyOffice::leftjoin('supply_requests','company_received','=','id_company_office')->where('visible_business','1')->where('partita_iva_company',$data['research'])->where('block',null)->where('supply',null)->join('comuni','id_comune','=','cap_company')->leftJoin('company_offices_extra_italia','id_company_office_extra','id_company_office')->join('employees','company_employee','=','id_company_office')->where('responsabile','=','1')->join('users','id','=','user_employee')->where('company_requested','=',null)->where('company_received','=',null)->select('rag_soc_company','nazione_company','indirizzo_company','civico_company','telefono_company','fax_company','email_company','cap','comune','sigla_prov','cap_company_office_extra','city_company_office_extra','state_company_office_extra','name','cognome','id_company_office')->get();
            $employee = Employee::join('users','user_employee','=','id')->where('id',Auth::id())->select('matricola','tel_employee','cell_employee','employees.created_at','email')->get();
            if(count($business)==0){
                session()->flash('message', 'Non ci sono aziende da mostrare con questa partita iva');
                return redirect()->route('supplyresearch');
            } else {
                if(count($business)==1){
                    $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
                    foreach ($business as $azienda) $block = $azienda['id_company_office'];
                    if ($block==$company->company_employee){
                        session()->flash('message', 'Non ci sono aziende da mostrare con questa partita iva');
                        return redirect()->route('supplyresearch');
                    }
                }
                return view('employee.companiesfound', [
                    'dati' => $dati[0],
                    'employee' => $employee[0],
                    'business' => $business,
                ]);
            }
        } else return view('errors.500');
    }

    public function requestSupply($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $up = SupplyRequest::create(
                [
                  'company_requested' =>  $company->company_employee,
                  'company_received' => $id,
                  'recipient' => '1',
                ]
            );
            $up2 = SupplyRequest::create(
                [
                    'company_requested' =>  $id,
                    'company_received' => $company->company_employee,
                ]
            );
            if ($up and $up2){
                $email = Employee::join('users','id','=','user_employee')->where('responsabile','1')->where('company_employee',$id)->select('name','cognome','email')->get();
                foreach ($email as $em){
                    Mail::to($em->email)->send(new AggregationRequest($em));
                }
                $messaggio = $up ? 'La richiesta di aggregazione è stata trasmessa' : 'Problemi con il server riprovare';
                session()->flash('message', $messaggio);
                return redirect()->route('employee');
            }
        } else return view('errors.500');
    }

    public function requestsTransmitted(){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1'){
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $trasmit = CompanyOffice::join('supply_requests','company_received','=','id_company_office')->where('company_requested',$company->company_employee)->where('block','0')->where('supply','0')->where('recipient','1')->join('comuni','id_comune','=','cap_company')->leftJoin('company_offices_extra_italia','id_company_office_extra','id_company_office')->select('company_received','rag_soc_company','partita_iva_company','nazione_company','indirizzo_company','civico_company','cap','comune','sigla_prov','cap_company_office_extra','city_company_office_extra','state_company_office_extra')->paginate(env('PAGINATE_COMPANY'));
            if (count($trasmit)==0) {
                session()->flash('message', 'Non ci sono richieste di aggregazione in corso');
                return redirect()->route('employee');
            }
            $dato = $this->dataProfile();
            return view('employee.supply-requests',
                [
                    'dati' => $dato[0],
                    'company' =>$trasmit,

                ]);
        } else return ('errors.500');
    }

    public function cancelRequest($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $del = SupplyRequest::where('company_requested',$company->company_employee)->where('company_received',$id)->delete();
            $del = SupplyRequest::where('company_requested',$id)->where('company_received',$company->company_employee)->delete();
            $email = Employee::join('users','id','=','user_employee')->where('responsabile','1')->where('company_employee',$id)->select('name','cognome','email')->get();
            foreach ($email as $em){
                Mail::to($em->email)->send(new AggregationCancel($em));
            }
            $messaggio = $del ? 'La richiesta di aggregazione è stata annullata' : 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('employee');
        } else return view('errors.500');
    }

    public function retransmitRequest($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1'){
            $email = Employee::join('users','id','=','user_employee')->where('responsabile','1')->where('company_employee',$id)->select('name','cognome','email')->get();
            foreach ($email as $em){
                Mail::to($em->email)->send(new AggregationRequest($em));
            }
            session()->flash('message', 'La richiesta di aggregazione è stata ritrasmessa');
            return redirect()->route('employee');
        } else return ('errors.500');
    }

    public function requestsReceived(){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1'){
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $trasmit = CompanyOffice::join('supply_requests','company_requested','=','id_company_office')->where('company_received',$company->company_employee)->where('block','0')->where('supply','0')->where('recipient','1')->join('comuni','id_comune','=','cap_company')->leftJoin('company_offices_extra_italia','id_company_office_extra','id_company_office')->select('company_requested','rag_soc_company','partita_iva_company','nazione_company','indirizzo_company','civico_company','cap','comune','sigla_prov','cap_company_office_extra','city_company_office_extra','state_company_office_extra')->paginate(env('PAGINATE_COMPANY'));
            if (count($trasmit)==0) {
                session()->flash('message', 'Non ci sono richieste di aggregazione in corso');
                return redirect()->route('employee');
            }
            $dato = $this->dataProfile();
            return view('employee.supply-received',
                [
                    'dati' => $dato[0],
                    'company' =>$trasmit,

                ]);
        } else return ('errors.500');
    }

    public function blockRequest($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            SupplyRequest::where('company_requested',$id)->where('company_received',$company->company_employee)->update(
                [
                    'block' => 1,
                ]
            );
            $up = SupplyRequest::where('company_requested',$company->company_employee)->where('company_received',$id)->update(
                [
                    'block' => 1,
                ]
            );
            $messaggio = $up ? 'L\'azienda è stata bloccata' : 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('employee');
        }
    }

    public function cancelCompanyRequest($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            SupplyRequest::where('company_requested',$id)->where('company_received',$company->company_employee)->delete();
            $up = SupplyRequest::where('company_requested',$company->company_employee)->where('company_received',$id)->delete();
            $email = Employee::join('users','id','=','user_employee')->join('company_offices','id_company_office','company_employee')->where('responsabile','1')->where('company_employee',$id)->select('name','cognome','email','rag_soc_company')->get();
            foreach ($email as $em){
                Mail::to($em->email)->send(new AggregationCancel($em));
            }
            $messaggio = $up ? 'La richiesta di aggregazione è stata annullata' : 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('employee');
        }
    }

    public function AcceptRequest($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $up = SupplyRequest::where('company_requested',$id)->where('company_received',$company->company_employee)->update(
                [
                    'supply' => 1,
                ]
            );
            $up = SupplyRequest::where('company_requested',$company->company_employee)->where('company_received',$id)->update(
                [
                    'supply' => 1,
                ]
            );
            SupplyChain::create(
              [
                  'company_supply_shares' => $id,
                  'company_supply_received' => $company->company_employee,
              ]
            );
            SupplyChain::create(
                [
                    'company_supply_shares' => $company->company_employee,
                    'company_supply_received' => $id,
                ]
            );
            $email = Employee::join('users','id','=','user_employee')->join('company_offices','id_company_office','company_employee')->where('responsabile','1')->where('company_employee',$id)->select('name','cognome','email','rag_soc_company')->get();
            foreach ($email as $em){
                Mail::to($em->email)->send(new AggregationAccepted($em));
            }
            $messaggio = $up ? 'L\'azienda è stata inserita nella tua Supply Chain' : 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('employee');
        }
    }

    public function supplyChainManagement(){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1'){
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $trasmit = CompanyOffice::join('supply_requests','company_received','=','id_company_office')->where('company_requested',$company->company_employee)->where('block','0')->where('supply','1')->join('comuni','id_comune','=','cap_company')->leftJoin('company_offices_extra_italia','id_company_office_extra','id_company_office')->select('company_received','rag_soc_company','partita_iva_company','nazione_company','indirizzo_company','civico_company','cap','comune','sigla_prov','cap_company_office_extra','city_company_office_extra','state_company_office_extra')->paginate(env('PAGINATE_COMPANY'));
            if (count($trasmit)==0) {
                session()->flash('message', 'Non ci sono aziende in aggregazione');
                return redirect()->route('employee');
            }
            $dato = $this->dataProfile();
            return view('employee.supplychainmanagement',
                [
                    'dati' => $dato[0],
                    'company' =>$trasmit,

                ]);
        } else return ('errors.500');
    }

    public function deleteSupplyChain($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $rag_soc = CompanyOffice::where('id_company_office',$company->company_employee)->select('rag_soc_company')->first();
            $rag = strtoupper($rag_soc->rag_soc_company);
            $up1 = SupplyRequest::where('company_requested',$id)->where('company_received',$company->company_employee)->delete();
            $up2 = SupplyRequest::where('company_requested',$company->company_employee)->where('company_received',$id)->delete();
            $up3 = SupplyChain::where('company_supply_shares',$id)->where('company_supply_received',$company->company_employee)->delete();
            $up4 = SupplyChain::where('company_supply_shares',$company->company_employee)->where('company_supply_received',$id)->delete();
            $email = Employee::join('users','id','=','user_employee')->join('company_offices','id_company_office','company_employee')->where('responsabile','1')->where('company_employee',$id)->select('name','cognome','email','rag_soc_company')->get();
            foreach ($email as $em){
                Mail::to($em->email)->send(new DeleteSupplyChain($em,$rag));
            }
            if ($up1 and $up2 and $up3 and $up4) $messaggio = 'L\'azienda è stata eliminata dalla tua Supply Chain'; else
                $messaggio = 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('employee');
        } else return ('errors.500');
    }

    public function managerSupplyChain($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $data = $this->dataProfile();
            foreach ($data as $datum)
                $company = $datum->id_company_office;
            $rag_soc = CompanyOffice::where('id_company_office',$id)->select('rag_soc_company','id_company_office')->get();
            $supply = SupplyChain::where('company_supply_received',$id)->where('company_supply_shares',$company)->select('forecast','availability','b2b','ean_mapping','created_at','updated_at')->get();
            return view('employee.sharingmanagement',[
                'dati' => $data[0],
                'supply' => $supply[0],
                'rag_soc' => $rag_soc[0],
            ]);
        } else return ('errors.500');
    }

    public function updateSupplyChain(Request $request){
        $data = $request->all();
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $rag_soc = CompanyOffice::where('id_company_office',$company->company_employee)->select('rag_soc_company')->first();
            $rag = strtoupper($rag_soc->rag_soc_company);
            if (!isset($data['forecast'])) $data['forecast']='0';
            if (!isset($data['ean_mapping'])) $data['ean_mapping']='0';
            if (!isset($data['availability'])) $data['availability']='0';
            if (!isset($data['b2b'])) $data['b2b']='0';
            $up = SupplyChain::where('company_supply_shares',$company->company_employee)->where('company_supply_received',$data['company_supply_received'])->update(
              [
                  'forecast' => $data['forecast'],
                  'availability' => $data['availability'],
                  'b2b' => $data['b2b'],
                  'ean_mapping' => $data['ean_mapping'],
              ]
            );
            $messaggio = '';
            if($data['forecast']=='1') $messaggio = $messaggio.'| La previsione sulle vendite ';
            if($data['ean_mapping']=='1') $messaggio = $messaggio.'| La mappatura dei prodotti tramite codice a barre EAN ';
            if($data['availability']=='1') $messaggio = $messaggio.'| La giacenza effettiva delle proprie merci ';
            if($data['b2b']=='1') $messaggio = $messaggio.'| I prezzi riservati ai rivenditori';
            $email = Employee::join('users','id','=','user_employee')->join('company_offices','id_company_office','company_employee')->where('responsabile','1')->where('company_employee',$data['company_supply_received'])->select('name','cognome','email')->get();
            foreach ($email as $em){
                Mail::to($em->email)->send(new InformationSupply($em,$messaggio,$rag));
            }
            if (($data['availability']=='1') or ($data['b2b']=='1')){
                $find = Provider::where('company_provider',$data['company_supply_received'])->where('provider_supply',$company->company_employee)->select('id_provider')->get();
                if (count($find)==0){
                    $info = CompanyOffice::where('id_company_office',$company->company_employee)->join('comuni','id_comune','=','cap_company')->leftJoin('company_offices_extra_italia','company_office','=','id_company_office')->select('rag_soc_company','telefono_company','email_company','indirizzo_company','civico_company','cap','comune','sigla_prov','cap_company_office_extra','city_company_office_extra','state_company_office_extra')->first();
                    if ($info->cap=='8092')
                     $adress = $info->indirizzo_company.', '.$info->civico_company.' '.$info->cap_company_office_extra.' '.$info->city_company_office_extra.' '.$info->state_company_office_extra;
                    else
                        $adress = $info->indirizzo_company.', '.$info->civico_company.' '.$info->cap.' '.$info->comune.' ('.$info->sigla_prov.')';
                    Provider::create(
                        [
                            'company_provider' => $data['company_supply_received'],
                            'supply_provider' => '1',
                            'provider_supply' => $company->company_employee,
                            'provider_cod' => 'SUPPLY'.$company->company_employee,
                            'rag_soc_provider' => $info->rag_soc_company,
                            'telefono_provider' => $info->telefono_company,
                            'email_provider' => $info->email_company,
                            'address_provider' => $adress,
                        ]
                    );
                }
            }
            $messaggio = $up ? 'La condivisione delle informazioni è stata aggiornata' : 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('supplychainmanagement');
        }
    }

    public function ViewBlockSupply(){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $trasmit = CompanyOffice::join('supply_requests','company_requested','=','id_company_office')->where('company_received',$company->company_employee)->where('block','1')->where('supply','0')->where('recipient','1')->join('comuni','id_comune','=','cap_company')->leftJoin('company_offices_extra_italia','id_company_office_extra','id_company_office')->select('company_requested','rag_soc_company','partita_iva_company','nazione_company','indirizzo_company','civico_company','cap','comune','sigla_prov','cap_company_office_extra','city_company_office_extra','state_company_office_extra')->paginate(env('PAGINATE_COMPANY'));
            if (count($trasmit)==0) {
                session()->flash('message', 'Non ci sono aziende bloccate');
                return redirect()->route('employee');
            }
            $dato = $this->dataProfile();
            return view('employee.supply-block',
                [
                    'dati' => $dato[0],
                    'company' =>$trasmit,

                ]);
        } else return ('errors.500');
    }

    public function sblockRequest($id){
        $responsabile = $this->responsabileControl();
        if ($responsabile->responsabile=='1') {
            $company = Employee::where('user_employee',Auth::id())->select('company_employee')->first();
            $rag = CompanyOffice::where('id_company_office', $id)->select('rag_soc_company')->first();
            SupplyRequest::where('company_requested',$id)->where('company_received',$company->company_employee)->delete();
            $up = SupplyRequest::where('company_requested',$company->company_employee)->where('company_received',$id)->delete();
            $messaggio = $up ? 'L\'azienda '.strtoupper($rag->rag_soc_company).' è stata sbloccata' : 'Problemi con il server riprovare';
            session()->flash('message', $messaggio);
            return redirect()->route('employee');
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function dataProfile(): \Illuminate\Support\Collection
    {
        $data = User::join('employees', 'id', '=', 'user_employee')->join('company_offices', 'employees.company_employee', '=', 'company_offices.id_company_office')->join('business_profiles', 'company_offices.id_admin_company', '=', 'business_profiles.id_admin')->join('comuni', 'comuni.id_comune', '=', 'company_offices.cap_company')->leftJoin('company_offices_extra_italia', 'company_offices_extra_italia.company_office', '=', 'company_offices.id_company_office')->where('id', Auth::id())->select('name', 'cognome', 'img_employee', 'responsabile', 'acquisti', 'produzione', 'vendite', 'rag_soc_company', 'cap_company', 'indirizzo_company', 'civico_company', 'logo', 'cap', 'comune', 'sigla_prov', 'cap_company_office_extra', 'city_company_office_extra', 'state_company_office_extra', 'nazione_company','id_company_office','provincia','visible_user','visible_business')->get();
        return $data;
    }

    public function responsabileControl(){
        $controllo = $this->block();
        if ($controllo){
            return view('errors.500');
        } else {
            $responsabile = Employee::where('user_employee','=',Auth::id())->select('responsabile','company_employee')->first();
            if($responsabile->responsabile=='1'){
                return $responsabile;
            }
            else {
                return view('errors.500');
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\NewEmployee;
use App\Http\Requests\Employee\PictureUpdate;
use App\Http\Requests\Employee\ProfileUpdate;
use App\Http\Requests\Employee\UpdateCompany;
use App\Models\CompanyOffice;
use App\Models\CompanyOfficeExtraItalia;
use App\Models\Employee;
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
                        if ($rag['img_employee']==null){
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
                        return redirect()->route('employee');
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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function dataProfile(): \Illuminate\Support\Collection
    {
        $data = User::join('employees', 'id', '=', 'user_employee')->join('company_offices', 'employees.company_employee', '=', 'company_offices.id_company_office')->join('business_profiles', 'company_offices.id_admin_company', '=', 'business_profiles.id_admin')->join('comuni', 'comuni.id_comune', '=', 'company_offices.cap_company')->leftJoin('company_offices_extra_italia', 'company_offices_extra_italia.company_office', '=', 'company_offices.id_company_office')->where('id', Auth::id())->select('name', 'cognome', 'img_employee', 'responsabile', 'acquisti', 'produzione', 'vendite', 'rag_soc_company', 'cap_company', 'indirizzo_company', 'civico_company', 'logo', 'cap', 'comune', 'sigla_prov', 'cap_company_office_extra', 'city_company_office_extra', 'state_company_office_extra', 'nazione_company','id_company_office','provincia')->get();
        return $data;
    }
}

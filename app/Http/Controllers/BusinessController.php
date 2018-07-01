<?php


namespace App\Http\Controllers;

use App\Http\Requests\addCompany;
use App\Http\Requests\NewCompany;
use App\Http\Requests\BusinessContattiUpdate;
use App\Http\Requests\BusinessDescUpdate;
use App\Http\Requests\BusinessLogoUpdate;
use App\Http\Requests\BusinessRegisterProfile;
use App\Http\Requests\BusinessUpdateProfile;
use App\Models\BusinessProfile;
use App\Models\BusinessProfileExtraItalia;
use App\Models\Categoria;
use App\Models\CompanyCategorie;
use App\Models\CompanyOffice;
use App\Models\CompanyOfficeExtraItalia;
use App\Models\Employee;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Comune;
use App\VerifyUser;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Storage;


class BusinessController extends Controller
{


    public function index(){
        $controllo = $this->block();
        if ($controllo==false){
            return $this->capBusiness('admin.admin');
        } else {
            return redirect()->route('completebusiness');
        }
    }

    public function viewProfile(){
        $controllo = $this->block();
        if ($controllo==false){
            $province = DB::table('comuni')->select('provincia')->groupBy('provincia')->orderBy('provincia')->get();
            $comuni= DB::table('comuni')->select('comune')->orderBy('comune')->get();
            $nazioni = DB::table('stati')->select('nome_stati')->orderBy('nome_stati')->get();
            $cap = DB::table('business_profiles')->select('cap_busines')->where('id_admin', Auth::id())->get();
            foreach ($cap as $cap2) {
                if ($cap2->cap_busines != '8092') {
                    $business = BusinessProfile::where('id_admin', Auth::id())->join('comuni', 'cap_busines', '=', 'id_comune')->get();
                } else {
                    $business = BusinessProfile::where('id_admin', Auth::id())->join('business_profiles_extra_italia', 'id_business_profile', '=', 'profilo')->get();
                }
                return view('admin.update', ['profile' => $business[0]])->with('nazioni', $nazioni)->with('province',$province)->with('comune',$comuni);
            }
        } else {
            return redirect()->route('completebusiness');
        }
    }

    public function block(){
        $profilo = Auth::user()->profile;
        if ($profilo=='0') {
            return true;
        }
        else return false;
    }

    public function registerProfile(){
        $province = DB::table('comuni')->select('provincia')->groupBy('provincia')->orderBy('provincia')->get();
        $nazioni = DB::table('stati')->select('nome_stati')->orderBy('nome_stati')->get();
        $partitaiva=DB::table('business_profiles')->select('partita_iva')->where('id_admin',Auth::user()->id)->get();
        return view('business.registerprofile')->with('nazioni', $nazioni)->with('province',$province)->with('partiva',$partitaiva);
    }

    public function profile(){
        $controllo = $this->block();
        if ($controllo==false){
            return view('business.user');
        } else {
            return view('business.registerbusiness');
        }
    }

    public function CreateBusinessProfile(BusinessRegisterProfile $request) {
        $data = $request->all();
        if ($data['nazione']=='Italia'){
            if (($data['comune']!='Seleziona prima la provincia') or ($data['comune']!=null)){
                $comune = DB::table('comuni')->select('id_comune')->where('comune',$data['comune'])->get();
                foreach ($comune as $cap){
                    $data['cap'] = $cap->id_comune;
                }
                //$comune2 = json_encode($comune);
                //$comune3 = explode(':',$comune2);
                //$lung = strlen($comune3[1]);
                //$data['cap'] = substr($comune3[1],0,  $lung-1);
            } else {
                return redirect()->route('registerbusiness');
            }
        } else{
            $data['cap']='8092';

        }
        if ($request['logo']!=null){
            $data = $this->processFile($request, $data);
        } else {
            $data['logo']=null;
        }
        $res = BusinessProfile::where('id_admin', Auth::id())->update(
          [
              'rag_soc' => $data['rag_soc'],
              'rea' => $data['rea'],
              'codice_fiscale' => $data['codice_fiscale'],
              'nazione' => $data['nazione'],
              'cap_busines' => $data['cap'],
              'indirizzo' => $data['indirizzo'],
              'civico' => $data['civico'],
              'telefono' => $data['telefono'],
              'cellulare' => $data['cellulare'],
              'fax' => $data['fax'],
              'pec' => $data['pec'],
              'web' => $data['web'],
              'descrizione' => $data['descrizione'],
              'logo' => $data['logo'],
          ]
        );
        if ($res) {
            if ($data['nazione']!='Italia') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $profilo = DB::table('business_profiles')->select('id_business_profile')->where(
                    'id_admin', Auth::id()
                )->get();
                foreach ($profilo as $pro) {
                    $data['profilo'] = $pro->id_business_profile;
                }
                $extra = BusinessProfileExtraItalia::create(
                    [
                        'cap_extra' => $data['cap_extra'],
                        'city' => $data['city'],
                        'state' => $data['state'],
                        'profilo' => $data['profilo'],
                    ]
                );
                if ($extra) {
                    $set = $this->SetProfile();
                }
            }
            if (!isset($set)){
                $set2 = $this->SetProfile();
            }
            if (((isset($set)) and $set==true) or ((isset($set2)) and $set2==true)) {
                return redirect()->route('admin');
            }
            else {
                return redirect()->route('registerbusiness');
            }
            }
        }

        public function updateProfile(BusinessUpdateProfile $request){
            $data = $request->all();
            if ($data['nazione']=='Italia') {
                if (($data['provincia']==null) or ($data['provincia']=='Seleziona la tua provincia')) {
                    $messaggio = 'Non hai selezionato la provincia';
                    session()->flash('message', $messaggio);
                    return redirect()->route('adminprofile');
                }
                if (($data['comune']!='Seleziona prima la provincia') or ($data['comune']!=null)){
                    $comune = DB::table('comuni')->select('id_comune')->where('comune',$data['comune'])->get();
                    foreach ($comune as $cap){
                        $data['cap'] = $cap->id_comune;
                    }
                } else {
                    return redirect()->route('adminprofile');
                }
            } else {
                if ($data['cap_extra']==null or $data['city']==null or $data['state']==null){
                    $messaggio = 'Non hai inserito i dati di residenza';
                    session()->flash('message', $messaggio);
                    return redirect()->route('adminprofile');
                } else {
                    $data['cap']='8092';
                }
            }
            $res = BusinessProfile::where('id_admin', Auth::id())->update(
                [
                    'rag_soc' => $data['rag_soc'],
                    'rea' => $data['rea'],
                    'codice_fiscale' => $data['codice_fiscale'],
                    'nazione' => $data['nazione'],
                    'cap_busines' => $data['cap'],
                    'indirizzo' => $data['indirizzo'],
                    'civico' => $data['civico'],
                ]
            );
            if ($res) {
                if ($data['nazione'] != 'Italia') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    $profilo = DB::table('business_profiles')->select('id_business_profile')->where(
                        'id_admin', Auth::id()
                    )->get();
                    foreach ($profilo as $pro) {
                        $up = $pro->id_business_profile;
                    }
                    $esiste = DB::table('business_profiles_extra_italia')->select('id_business_profile_extra')->where('profilo',$up)->get();
                    $aggiorna = false;
                    foreach ($esiste as $exist){
                        $aggiorna = true;
                        if ($exist->id_business_profile_extra!=null){
                            $extra = BusinessProfileExtraItalia::where('profilo', $up)->update(
                                [
                                    'cap_extra' => $data['cap_extra'],
                                    'city' => $data['city'],
                                    'state' => $data['state'],
                                    'profilo' => $up,
                                ]
                            );
                        }
                    }
                    if ($aggiorna==false){
                        $extra = BusinessProfileExtraItalia::create(
                            [
                                'cap_extra' => $data['cap_extra'],
                                'city' => $data['city'],
                                'state' => $data['state'],
                                'profilo' => $up,
                            ]
                        );
                    }
                    if ($extra) {
                        $messaggio = 'I dati sono stati aggiornati';
                        session()->flash('message', $messaggio);
                        return redirect()->route('admin');
                    }

                } else {

                        $esiste = DB::table('business_profiles_extra_italia')->select('id_business_profile_extra')->where('profilo', $data['id_business_profile'])->get();
                        $cancella = false;
                        foreach ($esiste as $elimina) {
                            if ($elimina->id_business_profile_extra!=null) {
                                $cancella = true;
                                $del = $elimina->id_business_profile_extra;
                                $delete = BusinessProfileExtraItalia::where('id_business_profile_extra', $del)->delete();
                                if ($delete) {
                                    $messaggio = 'La sede dell\'azienda è stata trasferita in Italia';
                                    session()->flash('message', $messaggio);
                                    return redirect()->route('admin');
                                }
                            }
                        }
                        if ($cancella==false){
                            $messaggio = 'I dati sono stati aggiornati';
                            session()->flash('message', $messaggio);
                            return redirect()->route('admin');
                        }

                    }
            } else {
                $messaggio = 'Problemi con il server riprova più tardi';
                session()->flash('message', $messaggio);
                return redirect()->route('adminprofile');
            }
        }

    public function logoView(){
        $controllo = $this->block();
        if ($controllo==false){
            $logo = DB::table('business_profiles')->select('logo')->where('id_admin', Auth::id())->get();
            return view('admin.logo', ['logo' => $logo[0]]);
        } else {
            return redirect()->route('completebusiness');
        }
    }

    public function updateLogo(BusinessLogoUpdate $request){
        if ($request['logo']!=null){
            $file = $request->file('logo');
            if ($file->isValid()) {
                $filename = 'admin' . Auth::id() . '.' . $file->extension();
                $file->storeAs(env('IMG_LOGO'), $filename);
                $data['logo'] = env('IMG_LOGO') . '/' . $filename;
                $upload = BusinessProfile::where('id_admin', Auth::id())->update(
                    [
                        'logo' => $data['logo'],
                    ]
                );
                if ($upload){
                    $messaggio = 'Il logo aziendale è stato aggiornato con successo';
                    session()->flash('message', $messaggio);
                    return redirect()->route('admin');
                } else {
                    $messaggio = 'Problemi di connessione con il server, riprovare più tardi';
                    session()->flash('message', $messaggio);
                    return redirect()->route('admin');
                }
            } else {
                $messaggio = 'Il file caricato non è un immagine';
                session()->flash('message', $messaggio);
                return redirect()->route('admin');
            }
        } else {
            $messaggio = 'Non è stato caricato un nuovo logo';
            session()->flash('message', $messaggio);
            return redirect()->route('admin');
        }
    }

    public function viewDesc(){
        $controllo = $this->block();
        if ($controllo==false){
            $descrizione = DB::table('business_profiles')->select('descrizione')->where('id_admin', Auth::id())->get();
            return view('admin.desc', ['desc' => $descrizione[0]]);
        } else {
            return redirect()->route('completebusiness');
        }
    }

    public function updateDesc(BusinessDescUpdate $request){
            $data = $request->descrizione;
            if ($data != null) {
                $res = BusinessProfile::where('id_admin', Auth::id())->update(
                    [
                        'descrizione' => $data,
                    ]
                );
                if ($res) {
                    $messaggio = 'La descrizione è stata aggiornata';
                    session()->flash('message', $messaggio);
                    return redirect()->route('admin');
                } else {
                    $messaggio = 'Problemi con il server riprova più tardi';
                    session()->flash('message', $messaggio);
                    return redirect()->route('admin');
                }
            } else {
                $messaggio = 'Non è stata aggiunta una nuova descrizione';
                session()->flash('message', $messaggio);
                return redirect()->route('admin');
            }

    }

    public function viewContatti(){
        $controllo = $this->block();
        if ($controllo==false){
            $contatti = DB::table('business_profiles')->select('web','telefono','cellulare','fax','pec')->where('id_admin', Auth::id())->get();
            return view('admin.contatti', ['contatti' => $contatti[0]]);
        } else {
            return redirect()->route('completebusiness');
        }
    }

    public function updateContatti(BusinessContattiUpdate $request){
        $data = $request->all();
        if ($data) {
            $res = BusinessProfile::where('id_admin',Auth::id())->update(
              [
                  'telefono' => $data['telefono'],
                  'cellulare' => $data['cellulare'],
                  'fax' => $data['fax'],
                  'pec' => $data['pec'],
                  'web' => $data['web'],
              ]
            );
            if ($res){
                $messaggio = 'I contatti sono stati aggiornati';
                session()->flash('message', $messaggio);
                return redirect()->route('admin');
            } else {
                $messaggio = 'Problemi con il server riprovare più tardi';
                session()->flash('message', $messaggio);
                return redirect()->route('admin');
            }
        } else {
            $messaggio = 'Non sono stati inseriti nuovi contatti';
            session()->flash('message', $messaggio);
            return redirect()->route('admin');
        }
    }

    public function SetProfile () {
        $res = DB::table('users')->where('id', Auth::id())->update(
            ['profile' => 1]
        );
        if ($res) return true;
        else return false;
    }

    /**
     * @param BusinessRegisterProfile $request
     * @param $data
     * @return mixed
     */
    public function processFile(BusinessRegisterProfile $request, $data)
    {
        if ($data['logo']) {
            $file = $request->file('logo');
            if ($file->isValid()) {

                $filename = 'admin' . Auth::id() . '.' . $file->extension();
                $file->storeAs(env('IMG_LOGO'), $filename);
                $data['logo'] = env('IMG_LOGO') . '/' . $filename;
            }
        }
        return $data;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function capBusiness($view)
    {
        $cap = DB::table('business_profiles')->select('cap_busines')->where('id_admin', Auth::id())->get();
        foreach ($cap as $cap2) {
            if ($cap2->cap_busines != '8092') {
                $business = BusinessProfile::where('id_admin', Auth::id())->join('comuni', 'cap_busines', '=', 'id_comune')->get();
            } else {
                $business = BusinessProfile::where('id_admin', Auth::id())->join('business_profiles_extra_italia', 'id_business_profile', '=', 'profilo')->get();
            }
            return view($view, ['profile' => $business[0]]);
        }
    }

    public function addCompany(addCompany $request){
        $data = $request->only('default_azienda','default_admin','extra');

        //categorie merceologiche
        $categorie = Categoria::select('categoria','id_categoria')->orderBy('categoria')->get();

        //Default azienda
        if ((isset($data['default_azienda']) and $data['default_azienda']=='1')){
            $cap = DB::table('business_profiles')->select('cap_busines')->where('id_admin', Auth::id())->get();
            foreach ($cap as $cap2) {
                if ($cap2->cap_busines != '8092') {
                    $business = BusinessProfile::where('id_admin', Auth::id())->join('comuni', 'cap_busines', '=', 'id_comune')->get();
                } else {
                    $business = BusinessProfile::where('id_admin', Auth::id())->join('business_profiles_extra_italia', 'id_business_profile', '=', 'profilo')->get();
                }
            }
            $province = DB::table('comuni')->select('provincia')->groupBy('provincia')->orderBy('provincia')->get();
            $comuni= DB::table('comuni')->select('comune','id_comune')->orderBy('comune')->get();
            $nazioni = null;
            $data['extra']='0';
        } else{
            //Estero
            if ((isset($data['extra']) and $data['extra']=='1')){
                $nazioni = DB::table('stati')->select('nome_stati')->orderBy('nome_stati')->get();
                $province = null;
                $comuni= null;
            } else{
                $data['extra']='0';
                $province = DB::table('comuni')->select('provincia')->groupBy('provincia')->orderBy('provincia')->get();
                $comuni= DB::table('comuni')->select('comune','id_comune')->orderBy('comune')->get();
                $nazioni = null;
            }
            $data['default_azienda']='0';
            $business=null;
        }

        //Default admin
        if ((isset($data['default_admin']) and $data['default_admin']=='1')){
            $admin = User::whereKey(Auth::id())->select('name','cognome','email')->get();
        } else{
            $data['default_admin']='0';
            $admin = null;
            if ((isset($data['default_azienda']) and $data['default_azienda']=='1')){
                $admin = User::whereKey(Auth::id())->select('email')->get();
            }
        }

        if ($business==null){
            $business=[
                '1' => '1',
            ];
        }

        if ($admin==null){
            $admin=[
              '1' => '1',
            ];
        }

        if ($comuni==null){
            $comuni = [
              '1' => '1',
            ];
        }

        if ($province==null){
            $province =[
              '1' =>'1',
            ];
        }

        return view('admin.newcompany', [
            'categorie' => $categorie,
            'default_azienda' => $data['default_azienda'],
            'extra' => $data['extra'],
            'default_admin' => $data['default_admin'],
            'admi' => $admin,
            'busines' => $business,
            'nazioni' => $nazioni,
            'province' => $province,
            'comune' => $comuni,
        ]);


    }

    public function addNewCompany(NewCompany $request){
        $data = $request->all();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $res = CompanyOffice::create(
          [
              'rag_soc_company' => $data['rag_soc_company'],
              'nazione_company' => $data['nazione_company'],
              'indirizzo_company' => $data['indirizzo_company'],
              'civico_company' => $data['civico_company'],
              'cap_company' => $data['cap_company'],
              'partita_iva_company' => $data['partita_iva_company'],
              'codice_fiscale_company' => $data['codice_fiscale_company'],
              'telefono_company' => $data['telefono_company'],
              'cellulare_company' => $data['cellulare_company'],
              'fax_company' => $data['fax_company'],
              'email_company' => $data['email_company'],
              'id_admin_company'=> Auth::id()
          ]
        );
        if ($res){
            $id=($res->id_company_office);
            //Registrazione indirizzo estero
            if ($data['cap_company']=='8092'){
                CompanyOfficeExtraItalia::create(
                    [
                        'cap_company_office_extra' => $data['cap_company_office_extra'],
                        'city_company_office_extra' => $data['city_company_office_extra'],
                        'state_company_office_extra' => $data['state_company_office_extra'],
                        'company_office' => $id
                    ]
                );
            }
            //Registrazione categorie
            foreach ($data['categoria'] as $categoria){
                CompanyCategorie::create(
                    [
                        'company' => $id,
                        'categoria' => $categoria
                    ]
                );
            }
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

                //Profilo impiegato
                if ($request['img_employee']!=null){
                    $file = $request->file('img_employee');
                    if ($file->isValid()) {
                        $filename = 'employee' . $id_user .'-company'. $data['rag_soc_company'] . '.' . $file->extension();
                        $file->storeAs(env('IMG_PROFILE'), $filename);
                        $data['img_employee'] = env('IMG_PROFILE') . '/' . $filename;
                    }
                } else {
                    $data['img_employee']=null;
                }
                $profile = Employee::create(
                  [
                      'user_employee' => $id_user,
                      'matricola' => $data['matricola'],
                      'tel_employee'=> $data['tel_employee'],
                      'cell_employee' => $data['cell_employee'],
                      'img_employee' => $data['img_employee'],
                      'company_employee' => $id,
                      'responsabile' => '1',
                      'acquisti' => '1',
                      'produzione' => '1',
                      'vendite' => '1'
                  ]
                );
                if ($profile){
                    User::where('id',$id_user)->update(
                        [
                            'profile' => '1'
                        ]
                    );
                    $messaggio = 'La sede è stata creata con successo';
                    session()->flash('message', $messaggio);
                    return redirect()->route('admin');
                }
            } else{
                $messaggio = 'Problemi con il server riprovare più tardi';
                session()->flash('message', $messaggio);
                return redirect()->route('admin');
            }
        }

    }

    public function viewCompany(){
        $controllo = $this->block();
        if ($controllo==false) {
            $company = User::join('employees', 'id', '=', 'user_employee')->where('employees.responsabile', '=', '1')->join('company_offices', 'employees.company_employee', '=', 'company_offices.id_company_office')->where('company_offices.id_admin_company', '=', Auth::id())->join('comuni', 'cap_company', '=', 'id_comune')->leftJoin('company_offices_extra_italia', 'company_offices.id_company_office', '=', 'company_offices_extra_italia.company_office')->select('rag_soc_company', 'cap_company', 'comune', 'provincia', 'indirizzo_company', 'civico_company', 'tel_employee', 'img_employee', 'name', 'cognome','company_offices.created_at', 'nazione_company', 'cap_company_office_extra', 'city_company_office_extra', 'state_company_office_extra','id_company_office')->orderBy('rag_soc_company')->paginate(env('PAGINATE_COMPANY'));
            return view('admin.viewcompany',[
              'company' => $company,
            ]);
        } else {
            return redirect()->route('completebusiness');
        }
    }

    public function deleteCompany($id_company){
        $controllo = $this->block();
        if ($controllo==false) {
            //eliminiamo gli impiegati dalla tabella users
            $delete = Employee::select('user_employee','img_employee')->where('company_employee',$id_company)->get();
            if ($delete){
                foreach ($delete as $user){

                    $del = DB::table('users')->where('id','=',$user->user_employee)->delete();
                    if ($del){
                        //delete storage
                        if ($user->img_employee!=null){
                            $file = $user->img_employee;
                            $disk = config('filesystems.default');
                            if ($file && Storage::disk($disk)->has($file)){
                                Storage::disk($disk)->delete($file);
                            }
                        }
                    }
                }
            }
            if ($del){
                //eliminiamo la sede
                $del_sede = DB::table('company_offices')->where('id_company_office',$id_company)->delete();
                if ($del_sede){
                    $messaggio = 'La sede è stata eliminata dal sistema';
                    session()->flash('message', $messaggio);
                    return redirect()->route('admin');
                } else {
                    $messaggio = 'Problemi momentanei con il server, riprovare più tardi';
                    session()->flash('message', $messaggio);
                    return redirect()->route('admin');
                }
            }
        }
        else {
            return redirect()->route('completebusiness');
        }
    }
}

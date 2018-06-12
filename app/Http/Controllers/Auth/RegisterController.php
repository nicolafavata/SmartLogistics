<?php

namespace App\Http\Controllers\Auth;

use App\Models\BusinessProfile;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\CompanyOffice;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'name' => 'required|string|max:255|min:3',
            'cognome' => 'required|string|max:255|min:3',
            'business' => 'required|boolean',
            'gdpr'=> 'required',
            'partiva' => 'string|digits:11|unique:company_offices,partita_iva_company|unique:business_profiles,partita_iva',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|alpha_dash|min:8|confirmed',
        ], $this->errorMessages);
    }

    protected $errorMessages = [
        'partiva.digits' => 'La partita iva deve avere 11 cifre',
        'partiva.string' => 'La partita iva deve avere 11 cifre',
        'partiva.unique' => 'La partita iva risulta già presente nei nostri archivi',
        'name.required' => 'Devi inserire il tuo nome',
        'name.string' => 'Devi inserire il tuo nome',
        'name.max' => 'Hai un nome strano',
        'name.min' => 'Hai un nome strano',
        'cognome.required' => 'Devi inserire il tuo cognome',
        'cognome.string' => 'Devi inserire il tuo cognome',
        'cognome.min' => 'Hai un cognome strano',
        'cognome.max' => 'Hai un cognome strano',
        'business.required' => 'Devi selezionare se sei un azienda o un cittadino',
        'business.boolean' => 'Devi selezionare se sei un azienda o un cittadino',
        'gdpr.required' => 'Devi accettare la Ns. politica sulla privacy',
        'email.required' => 'Devi inserire la tua email',
        'email.string' => 'Inserisci un email corretta',
        'email.email' => 'Inserisci un email corretta',
        'email.max' => 'Inserisci un email corretta',
        'email.unique' => 'L \'email inserita risulta già registrata',
        'password.required' => 'Devi inserire una password di almeno 8 caratteri alfanumerici',
        'password.alpha_dash' => 'Devi inserire una password di almeno 8 caratteri alfanumerici',
        'password.string' => 'Devi inserire una password di almeno 8 caratteri alfanumerici',
        'password.min' => 'Devi inserire una password di almeno 8 caratteri alfanumerici',
        'password.confirmed' => 'La password confermata non coincide',
    ];

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
       // $registra=true;
        if ($data['business']=="0")
            $admin='0';

        else  $admin='1';
      //  {
            //$ricerca_partiva=$data['partiva'];
            //$sel= DB::table('company_offices')->where('partita_iva_company',$ricerca_partiva)->exists();
            //if ($sel == true) {
             //   $registra=false;

                //Session::flash('MessagePiva'=>'La partita iva risulta già presente nei nostri archivi');
            //} else
            //{
             //   $controllo=$this->CheckPiva($ricerca_partiva);
              //  if ($controllo == true) {
                 //   $admin='1';
               // } else {
                 //   $registra=false;
                 //   Session::flash('MessagePiva'=>'La partita iva non è corretta');
               // }
        //    }
      //  }
       // if ($registra==true){

            $push = User::create([
                'business'=>$data['business'],
                'name' => $data['name'],
                'admin' => $admin,
                'cognome'=>$data['cognome'],
                'gdpr'=>$data['gdpr'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            if (($push==true) & ($data['business']=="1")){
                $id=($push->id);
                BusinessProfile::create([
                    'partita_iva' => $data['partiva'],
                    'id_admin' => $id,
                ]);

            }
            return $push;
    }




    }
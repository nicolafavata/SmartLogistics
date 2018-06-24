<?php

namespace App\Http\Controllers\Auth;

use App\Mail\VerifyMail;
use App\Models\BusinessProfile;
use App\User;
use App\Http\Controllers\Controller;
use App\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
            'partiva' => 'string|digits:11|unique:business_profiles,partita_iva',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
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
        'password.required' => 'La password deve avere minimo 8 caratteri',
        'password.string' => 'La password deve avere minimo 8 caratteri',
        'password.min' => 'La password deve avere minimo 8 caratteri',
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
        if ($data['business']=="0")
            $admin='0';

        else  $admin='1';
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
            if ($push==true){
                $verifyUser = VerifyUser::create([
                    'user_id' => $push->id,
                    'token' => str_random(40),
                ]);
            }
            if($push==true){
                Mail::to($push->email)->send(new VerifyMail($push));
            }
            return $push;
    }

    public function verifyUser($token){
        $verifyUser = VerifyUser::where('token',$token)->first();
        if(isset($verifyUser)){
            $user = $verifyUser->user;
            if (!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $status = "La tua email è stata verificata. Puoi accedere a SmartLogis.";
            } else{
                $status= "La tua email è verificata. Puoi accedere.";
            }
        } else {
            return redirect('/login')->with('warning','Ci dispiace ma la tua email non è stata ancora verificata.');
        }

        return redirect('/login')->with('status',$status);
    }

    public function registered(Request $request, $user)
    {
        $this->guard()->logout();
        return redirect('/login')->with('status','Ti abbiamo trasmesso un codice d\'attivazione. Controlla la tua casella di posta');
    }

}
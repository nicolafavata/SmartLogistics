<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UsersProfile;

class UserController extends Controller
{
    public function block(){
        $profilo = Auth::user()->profile;
        if ($profilo=='0') {
            return true;
        }
        else return false;
    }

    public function index(){
        $controllo = $this->block();
        if ($controllo==false){
            return redirect()->route('geocode');
        } else {
            return redirect()->route('completeuser');
        }
    }


    public function geocode(){
        return view('user.geo');
    }

    public function geostore(Request $request) {
        $user = DB::table('users')->where('id',Auth::user()->id)->get();
        $comune = $user->comunenow;
        if ($comune != ($request->comune)) {
            $user->capnow=$request->cap;
            $user->comunenow=$request->comune;
            $user->save();
        }
        return redirect()->route('user');
    }

    public function registerProfile(){
        $province = DB::table('comuni')->select('provincia')->groupBy('provincia')->orderBy('provincia')->get();
        $nazioni = DB::table('stati')->select('nome_stati')->orderBy('nome_stati')->get();
        return view('user.registerprofile')->with('nazioni', $nazioni)->with('province',$province);
    }

    public function profile(){
        $nazioni = DB::table('stati')->select('nome_stati')->orderBy('nome_stati')->get();
        if (Auth::user()->profile=='1'){
            $profilo = DB::table('users_profiles')->select('*')->where('id_user', Auth::user()->id)->get();
            return view('user.profile')->withNazioni($nazioni)->withProfilo($profilo);
        } else
            return view('user.profile')->with('nazioni', $nazioni);
    }

    public function CreateUserProfile(){
        $data = request()->all();
        if ($data['nazione_user_profile']=='Italia'){
           if ($data['comune']!='Seleziona prima la provincia'){
                $comune = DB::table('comuni')->select('id_comune')->where('comune',$data['comune'])->get();
                $data['cap_user_profile']=$comune[0];
           } else{
               $data['cap_user_profile']=null;
               $data['indirizzo_user_profile']=null;
               $data['civico_user_profile']=null;
           }
        }
        $res = UsersProfile::create(
          [
              'sesso' => $data['sesso'],
              'nascita' => $data['nascita'],
              'nazione_user_profile' => $data['nazione_user_profile'],
              'indirizzo_user_profile' => $data['indirizzo_user_profile'],
              'civico_user_profile' => $data['civico_user_profile'],
              'cap_user_profile' => $data['cap_user_profile'],
              'partita_iva_user_profile'=> $data['partita_iva_user_profile'],
              'codice_fiscale_user_profile' => $data['codice_fiscale_user_profile'],
              'telefono_user_profile' => $data['telefono_user_profile'],
              'cellulare_user_profile' => $data['cellulare_user_profile'],
              'img_user_profile' => null,
              'id_user' => Auth::id(),
          ]
        );
        if ($res) {
            $res2 = DB::table('users')->where('id', Auth::id())->update(
                ['profile' => 1]
            );
            if ($res2) return redirect()->route('user');
            else return redirect()->route('registeruser');
        }
    }
}

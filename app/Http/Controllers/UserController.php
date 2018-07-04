<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterProfile;
use App\Models\UsersProfileExtraItalia;
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
            return view('user.user');
        } else {
            return redirect()->route('completeuser');
        }
    }


    public function geocode(){
        return view('user.geo');
    }

    public function geostore(Request $request) {
        $id = Auth::user()->id;
        $user = User::find($id);
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

    public function CreateUserProfile(UserRegisterProfile $request){
        $data = $request->all();
        if ($data['nazione_user_profile']=='Italia'){
           if ($data['comune']!='Seleziona prima la provincia'){
                $comune = DB::table('comuni')->select('id_comune')->where('comune',$data['comune'])->first();
                $data['cap_user_profile'] = $comune->id_comune;
           } else{
               $data['cap_user_profile']=null;
               $data['indirizzo_user_profile']=null;
               $data['civico_user_profile']=null;
           }
        } else{
            $data['cap_user_profile']='8092';
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
            if ($data['nazione_user_profile']!='Italia'){
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $profilo = DB::table('users_profiles')->select('id_user_profile')->where(
                    'id_user', Auth::id()
                )->get();
                foreach ($profilo as $pro) {
                    $data['user_extra_italia'] = $pro->id_user_profile;
                }
                $extra = UsersProfileExtraItalia::create(
                    [
                        'cap_user_profile_extra_italia' => $data['cap_user_profile_extra_italia'],
                        'city_user_profile_extra_italia' => $data['city_user_profile_extra_italia'],
                        'state_user_profile_extra_italia' => $data['state_user_profile_extra_italia'],
                        'user_extra_italia' => $data['user_extra_italia'],
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
                return view('user.geo');
            }
            else {
                return redirect()->route('registeruser');
            }
        }
    }

    public function SetProfile () {
        $res = DB::table('users')->where('id', Auth::id())->update(
            ['profile' => 1]
        );
        if ($res) return true;
        else return false;
    }
}

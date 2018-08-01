<?php

namespace App\Http\Controllers;

use App\Http\Requests\FindProduct;
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
            return view('user.user',[
                'product' => null
            ]);
        } else {
            return redirect()->route('completeuser');
        }
    }

    public function findProduct(FindProduct $request){
        $data = $request->all();
        $id = DB::table('comuni')->where('cap',Auth::user()->capnow)->select('id_comune')->first();
        $inventory = DB::table('visible_comuni')->where('cap_visible',$id->id_comune)->join('company_offices','company_office_visible','=','id_company_office')->join('sales_lists','company_sales_list','=','id_company_office')->leftJoin('inventories','id_inventory','=','inventory_sales_list')->where('ean_inventory',$data['product'])->where('visible_user','1')->where('visible_sales_list','1')->select('rag_soc_company','indirizzo_company','civico_company','telefono_company','email_company','price_user','quantity_sales_list','cod_inventory as cod_item','title_inventory as title','unit_inventory as unit','url_inventory as url','imposta_inventory as imposta','stock','brand','id_sales_list','id_inventory')->paginate(env('PAGINATE_ITEM'));
        $production = DB::table('visible_comuni')->where('cap_visible',$id->id_comune)->join('company_offices','company_office_visible','=','id_company_office')->join('sales_lists','company_sales_list','=','id_company_office')->leftJoin('productions','id_production','=','production_sales_list')->where('ean_production',$data['product'])->where('visible_user','1')->where('visible_sales_list','1')->select('rag_soc_company','indirizzo_company','civico_company','telefono_company','email_company','price_user','quantity_sales_list','cod_production as cod_item','title_production as title','unit_production as unit','url_production as url','imposta_production as imposta','id_production','company_production','brand_production as brand','id_sales_list','description_production as desc')->paginate(env('PAGINATE_ITEM'));
        if (count($inventory)>0){
            $i=0;
            foreach ($inventory as $t){
                $desc = DB::table('inventories')->where('id_inventory',$t->id_inventory)->select('description_inventory')->first();
                $inventory[$i]->desc = $desc->description_inventory;
                $i++;
            }
        }
        if (count($production)>0){
            $i=0;
            foreach ($production as $t){
                $stock = $this->CalculateQuantityProduction($t->id_production,$t->company_production,$t->unit);
                $production[$i]->stock = $stock;
                $i++;
            }
        }
        if (count($inventory)>0){
            $item = $inventory;
            if (count($production)>0){
                $k=count($inventory);
                foreach ($production as $t){
                    $item[$k]=$t;
                    $k++;
                }
            }
        } else {
            if (count($production)>0){
                $item = $production;
            } else $item=null;
        }
        if(count($item)>0){
            return view('user.foundproducts', [
                'item' => $item,
                'product' => $data['product']
            ]);
        } else {
            session()->flash('message', 'Il prodotto inserito non è presente nei negozi della tua città');
            return view('user.user', [
                'product' => $data['product']
            ]);
        }
    }

    public function CalculateQuantityProduction($id,$company,$um){
        $check = DB::table('inventories')->join('mapping_inventory_productions','inventory_map_pro','=','id_inventory')->where('production_map_pro',$id)->whereColumn('mapping_inventory_productions.quantity_mapping_production','>','inventories.stock')->get();
        if (count($check)>0) return 0;
        else {
            $maxquantity = DB::table('mapping_inventory_productions')->where('company_mapping_production',$company)->where('production_map_pro',$id)->select('quantity_mapping_production')->max('quantity_mapping_production');
            $code_inventory = DB::table('mapping_inventory_productions')->where('company_mapping_production',$company)->where('production_map_pro',$id)->where('quantity_mapping_production',$maxquantity)->select('inventory_map_pro')->first();
            $stock = DB::table('inventories')->where('id_inventory',$code_inventory->inventory_map_pro)->where('company_inventory',$company)->select('stock','committed')->first();
            $i=1;
            if(count($stock)>0){
                foreach ($stock as $x){
                    if ($i==1) $in = $x;
                    if ($i==2) $or = $x;
                    $i++;
                }
                if ($or > $in) $stock = 0; else $stock = $in - $or;
                if ($maxquantity>$stock) $available = 0; else $available = $stock / $maxquantity;
                if ($um=='NR') $available = floor($available);
                $check = DB::table('inventories')->join('mapping_inventory_productions','inventory_map_pro','=','id_inventory')->where('production_map_pro',$id)->where('id_inventory','<>',$code_inventory->inventory_map_pro)->select('id_inventory','stock','committed','quantity_mapping_production')->get();
                foreach ($check as $t){
                    $quantity = ($t->stock) - ($t->committed);
                    $can = -1;
                    if ($quantity>0){
                        while ($can<0){
                            $can = $quantity - ($available * $t->quantity_mapping_production);
                            if ($can<0) $available--;
                        }

                    }
                }
                if ($available<0) return 0; else return $available;
            } else return 0;
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

    public function viewProfile(){

        $province = DB::table('comuni')->select('provincia')->groupBy('provincia')->orderBy('provincia')->get();
        $nazioni = DB::table('stati')->select('nome_stati')->orderBy('nome_stati')->get();
        if (Auth::user()->profile=='1'){
            $profilo = DB::table('users_profiles')->select('*')->where('id_user', Auth::user()->id)->first();
            $comune = DB::table('comuni')->where('id_comune',$profilo->cap_user_profile)->select('*')->first();
            return view('user.viewprofile')->with('comune',$comune)->with('province',$province)->withNazioni($nazioni)->withProfilo($profilo);
        } else
            return view('user.profile')->with('nazioni', $nazioni);
    }

    public function updateProfile(UserRegisterProfile $request){
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
        $update = DB::table('users_profiles')->where('id_user',Auth::id())->update(
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
          ]
        );
        $mesaggio = $update ? 'Aggiornamento effettuato' : 'Problemi con il Server, riprova più tardi';
        session()->flash('message', $mesaggio);
        return redirect()->route('view-profile');
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

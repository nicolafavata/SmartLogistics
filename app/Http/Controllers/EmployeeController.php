<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function dataProfile(): \Illuminate\Support\Collection
    {
        $data = User::join('employees', 'id', '=', 'user_employee')->join('company_offices', 'employees.company_employee', '=', 'company_offices.id_company_office')->join('business_profiles', 'company_offices.id_admin_company', '=', 'business_profiles.id_admin')->join('comuni', 'comuni.id_comune', '=', 'company_offices.cap_company')->leftJoin('company_offices_extra_italia', 'company_offices_extra_italia.company_office', '=', 'company_offices.id_company_office')->where('id', Auth::id())->select('name', 'cognome', 'img_employee', 'responsabile', 'acquisti', 'produzione', 'vendite', 'rag_soc_company', 'cap_company', 'indirizzo_company', 'civico_company', 'logo', 'cap', 'comune', 'sigla_prov', 'cap_company_office_extra', 'city_company_office_extra', 'state_company_office_extra', 'nazione_company')->get();
        return $data;
    }
}

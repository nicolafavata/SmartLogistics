<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class UpdateCompany extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $BusinessProfile = DB::table('employees')->join('company_offices','id_company_office','=','company_employee')->join('business_profiles','id_admin','=','id_admin_company')->where('user_employee',Auth::id())->select('id_business_profile')->first();
        $company = DB::table('employees')->where('user_employee',Auth::id())->select('company_employee as id')->first();
        return [
            'rag_soc_company' => 'required|string|between:3,50',
            'partita_iva_company' => 'required|string|digits:11|unique:business_profiles,partita_iva,'.$BusinessProfile->id_business_profile.',id_business_profile',
            'codice_fiscale_company' => 'nullable|string|min:16|max:16',
            'nazione_company' => 'required|string|max:128',
            'cap_company' => 'required',
            'cap_company_office_extra' => 'nullable|string|max:8',
            'city_company_office_extra' => 'nullable|string|max:30',
            'state_company_office_extra' => 'nullable|string|max:30',
            'indirizzo_company' => 'required|string|max:30',
            'civico_company' => 'required|string|max:6',
            'telefono_company' => 'nullable|digits_between:5,16',
            'cellulare_company' => 'nullable|digits_between:5,16',
            'fax_company' => 'nullable|digits_between:5,16',
            'email_company' => 'required|string|email|max:255|unique:company_offices,email_company,'.$company->id.',id_company_office',
        ];
    }

    public function messages()
    {
        return [
            'telefono_company.digits_between' => 'Inserisci solo le cifre numeriche del recapito telefonico',
            'cellulare_company.digits_between' => 'Inserisci solo le cifre numeriche del recapito mobile',
            'fax_company.digits_between' => 'Inserisci solo le cifre numeriche del fax',
            'email_company.required' => 'Devi inserire l\'email della sede',
            'email_company.string' => 'Devi inserire l\'email della sede',
            'email_company.email' => 'Devi inserire l\'email della sede',
            'email_company.max' => 'Devi inserire l\'email della sede',
            'email_company.unique' => 'L\'email inserita appartiene già ad un\'altra azienda',
            'indirizzo_company.required' => 'L\'indirizzo non è stato inserito',
            'indirizzo_company.string' => 'Devi inserire l\'indirizzo',
            'indirizzo_company.max' => 'Devi inserire l\'indirizzo',
            'civico_company.required' => 'Il numero civico non è stato inserito',
            'civico_company.string' => 'Devi inserire il numero civico',
            'civico_company.max' => 'Devi inserire il numero civico',
            'cap_company_office_extra.string' => 'Non hai inserito il CAP',
            'cap_company_office_extra.max' => 'Il CAP inserito non è valido',
            'city_company_office_extra.string' => 'Non hai inserito tutti i dati di residenza',
            'city_company_office_extra.max' => 'Non hai inserito tutti i dati di residenza',
            'state_company_office_extra.string' => 'Non hai inserito tutti i dati di residenza',
            'state_company_office_extra.max' => 'Non hai inserito tutti i dati di residenza',
            'nazione_company.required' => 'Non hai selezionato la nazione',
            'nazione_company.string' => 'Non hai selezionato la nazione',
            'nazione_company.max' => 'Non hai selezionato la nazione',
            'cap_company.required' => 'Non hai selezionato il comune',
            'codice_fiscale_company.string' => 'Il codice fiscale deve essere alfanumerico',
            'codice_fiscale_company.min' => 'Il codice fiscale deve avere 16 caratteri',
            'codice_fiscale_company.max' => 'Il codice fiscale deve avere 16 caratteri',
            'partita_iva_company.required' => 'Devi inserire la partita iva della sede',
            'partita_iva_company.string' => 'Devi inserire la partita iva della sede',
            'partita_iva_company.digits' => 'La partita iva deve avere 11 cifre numeriche',
            'partita_iva_company.unique' => 'La partita iva appartiene ad un\'altra azienda registrata',
            'rag_soc_company.required' => 'Devi inserire la ragione sociale della sede',
            'rag_soc_company.string' => 'Devi inserire la ragione sociale della sede',
            'rag_soc_company.between' => 'La ragione sociale non può superare i 50 caratteri'
        ];
    }


}

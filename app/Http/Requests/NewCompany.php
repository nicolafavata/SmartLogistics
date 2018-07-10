<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class NewCompany extends FormRequest
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
        $BusinessProfile = DB::table('business_profiles')->select('id_business_profile')->where('id_admin',Auth::id())->first();
        return [
            'rag_soc_company' => 'required|string|between:3,50',
            'partita_iva_company' => 'required|string|digits:11|unique:business_profiles,partita_iva,'.$BusinessProfile->id_business_profile.',id_business_profile',
            'codice_fiscale_company' => 'nullable|string|min:16|max:16',
            'nazione_company' => 'required|string|max:128',
            'cap_company' => 'required',
            'categoria' => 'required',
            'cap_company_office_extra' => 'nullable|string|max:8',
            'city_company_office_extra' => 'nullable|string|max:30',
            'state_company_office_extra' => 'nullable|string|max:30',
            'indirizzo_company' => 'required|string|max:50',
            'civico_company' => 'required|string|max:6',
            'telefono_company' => 'nullable|digits_between:5,16',
            'cellulare_company' => 'nullable|digits_between:5,16',
            'fax_company' => 'nullable|digits_between:5,16',
            'email_company' => 'required|string|email|max:255',
            'name' => 'required|string|max:255|min:3',
            'cognome' => 'required|string|max:255|min:3',
            'matricola' => 'nullable|string|max:16',
            'tel_employee' => 'nullable|digits_between:5,16',
            'cell_employee' => 'nullable|digits_between:5,16',
            'img_employee' => 'nullable|image',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'email.required'=> 'Non hai inserito l\'email del responsabile dela sede',
            'email.string' => 'Non hai inserito l\'email del responsabile dela sede',
            'email.email' => 'Non hai inserito l\'email del responsabile dela sede',
            'email.max' => 'Non hai inserito l\'email del responsabile dela sede',
            'email.unique'  => 'L\'email del responsabile dela sede è già presente nei nostri archivi, devi inserire un\'altro indirizzo',
            'img_employee.image' => 'Il file del profilo deve essere di tipo immagine',
            'name.required' => 'Devi inserire il nome del responsabile della sede',
            'name.string' => 'Devi inserire il nome del responsabile della sede',
            'name.max' => 'Il nome del responsabile è troppo lungo',
            'name.min' => 'Il nome del responsabile è troppo breve',
            'cognome.required' => 'Devi inserire il cognome del responsabile della sede',
            'cognome.string' => 'Devi inserire il cognome del responsabile della sede',
            'cognome.max' => 'Il cognome del responsabile è troppo lungo',
            'cognome.min' => 'Il cognome del responsabile è troppo breve',
            'matricola.string' => 'Il numero di matricola inserito non è valido',
            'matricola.max' => 'Il numero di matricola inserito è troppo lungo',
            'telefono_company.digits_between' => 'Inserisci solo le cifre numeriche del recapito telefonico',
            'tel_employee.digits_between' => 'Inserisci solo le cifre numeriche del telefono del responsabile',
            'cell_employee.digits_between' => 'Inserisci solo le cifre numeriche del mobile del responsabile',
            'cellulare_company.digits_between' => 'Inserisci solo le cifre numeriche del recapito mobile',
            'fax_company.digits_between' => 'Inserisci solo le cifre numeriche del fax',
            'email_company.required' => 'Devi inserire l\'email della sede',
            'email_company.string' => 'Devi inserire l\'email della sede',
            'email_company.email' => 'Devi inserire l\'email della sede',
            'email_company.max' => 'Devi inserire l\'email della sede',
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
            'rag_soc_company.between' => 'La ragione sociale non può superare i 50 caratteri',
            'password.required' => 'Devi inserire una password di almeno 8 caratteri',
            'password.string' => 'Devi inserire una password di almeno 8 caratteri',
            'password.min' => 'Devi inserire una password di almeno 8 caratteri',
            'password.confirmed' => 'Devi confermare la password inserita',
            'categoria.required' => 'Devi selezionare le categorie merceologiche dell\'attività'
        ];
    }


}

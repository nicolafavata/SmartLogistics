<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRegisterProfile extends FormRequest
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
        return [
            'sesso' => 'required|string|max:1',
            'nascita' => 'nullable|date',
            'nazione_user_profile' => 'required|string|max:128',
            'indirizzo_user_profile' => 'nullable|string|max:30',
            'civico_user_profile' => 'nullable|string|max:6',
            'provincia' => 'nullable|string|max:45',
            'comune' => 'nullable|string|max:45',
            'partita_iva_user_profile'=> 'nullable|digits:11',
            'codice_fiscale_user_profile' => 'nullable|string|min:16|max:16',
            'telefono_user_profile' => 'nullable|digits_between:5,16',
            'cellulare_user_profile' => 'nullable|digits_between:5,16',
            'cap_user_profile_extra_italia' => 'nullable|string|max:8',
            'city_user_profile_extra_italia' => 'nullable|string|max:30',
            'state_user_profile_extra_italia' => 'nullable|string|max:30'
            ];
    }

    public function messages()
    {
        return [
            'sesso.required' => 'Devi selezionare il tuo sesso',
            'sesso.string' => 'Il sesso è una variabile stringa',
            'sesso.max' => 'Il sesso ha un solo carattere stringa',
            'nascita.date' => 'Controlla la data di nascita che hai inserito',
            'nazione_user_profile.required' => 'Devi selezionare la tua nazione',
            'nazione_user_profile.string' => 'Controlla la tua nazione',
            'nazione_user_profile.max' => 'Controlla la tua nazione',
            'indirizzo_user_profile.string' => 'Controlla il tuo indirizzo',
            'indirizzo_user_profile.max' => 'Controlla il tuo indirizzo',
            'civico_user_profile.string' => 'Controlla il numero civico',
            'civico_user_profile.max' => 'Il numero civico può avere al massimo 6 cifre',
            'provincia.string' => 'Controlla la provincia che hai selezionato',
            'provincia.max' => 'Controlla la provincia che hai selezionato',
            'comune.string' => 'Controlla il comune che hai selezionato',
            'comune.max' => 'Controlla il comune che hai selezionato',
            'partita_iva_user_profile.digits' => 'La partita iva deve avere 11 cifre numeriche',
            'codice_fiscale_user_profile.string' => 'Il codice fiscale deve avere 16 cifre alfanumeriche',
            'codice_fiscale_user_profile.min' => 'Il codice fiscale deve avere 16 cifre alfanumeriche',
            'codice_fiscale_user_profile.max' => 'Il codice fiscale deve avere 16 cifre alfanumeriche',
            'telefono_user_profile.digits_between' => 'Inserisci solo le cifre numeriche del tuo recapito telefonico',
            'cellulare_user_profile.digits_between' => 'Inserisci solo le cifre numeriche del tuo recapito cellulare',
            'cap_user_profile_extra_italia.string' => 'Controlla il CAP che hai inserito',
            'cap_user_profile_extra_italia.max' => 'Controlla il CAP che hai inserito',
            'city_user_profile_extra_italia.string' => 'Controlla la città che hai inserito',
            'city_user_profile_extra_italia.max' => 'Controlla la città che hai inserito',
            'state_user_profile_extra_italia.string' => 'Controlla lo stato che hai inserito',
            'state_user_profile_extra_italia.max' => 'Controlla lo stato che hai inserito',
            ];
    }
}

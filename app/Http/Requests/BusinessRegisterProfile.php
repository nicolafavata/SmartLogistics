<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BusinessRegisterProfile extends FormRequest
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
            'rag_soc' => 'required|string|between:3,50',
            'descrizione' => 'nullable|string|max:255',
            'nazione' => 'required|string|max:128',
            'indirizzo' => 'required|string|max:30',
            'civico' => 'required|string|max:6',
            'provincia' => 'nullable|string|max:45',
            'comune' => 'nullable|string|max:45',
            'cap_extra' => 'nullable|string|max:8',
            'city' => 'nullable|string|max:30',
            'state' => 'nullable|string|max:30',
            'codice_fiscale' => 'nullable|string|min:16|max:16',
            'rea' => 'required|string|max:8',
            'web' => 'nullable|string|max:30',
            'telefono' => 'nullable|digits_between:5,16',
            'cellulare' => 'nullable|digits_between:5,16',
            'fax' => 'nullable|digits_between:5,16',
            'pec' => 'nullable|email|max:30',
            'logo' => 'nullable|image',
        ];
    }

    public function messages()
    {
        return [
            'rag_soc.required' => 'Devi inserire la ragione sociale della tua azienda',
            'rag_soc.string' => 'Controlla la ragione sociale inserita',
            'rag_soc.between' => 'Controlla la ragione sociale inserita',
            'descrizione.string' => 'Controlla la descrizione dell\'azienda',
            'descrizione.max' => 'La descrizione dell\'azienda non può superare i 255 caratteri',
            'nazione.required' => 'Devi selezionare la nazione della tua azienda',
            'nazione.string' => 'Devi selezionare la nazione della tua azienda',
            'nazione.max' => 'Devi selezionare la nazione della tua azienda',
            'indirizzo.required' => 'Devi inserire l\'indirizzo completo e il comune di residenza',
            'indirizzo.string' => 'Devi inserire l\'indirizzo completo e il comune di residenza',
            'indirizzo.max' => 'L\'indirizzo di residenza non può superare i 30 caratteri',
            'civico.required' => 'Devi inserire Il numero civico e la residenza completa',
            'civico.string' => 'Devi inserire Il numero civico e la residenza completa',
            'civico.max' => 'Il numero civico non può superare i 6 caratteri',
            'provincia.string' => 'Devi selezionare la provincia',
            'provincia.max' => 'Devi selezionare la provincia',
            'comune.string' => 'Devi selezionare il comune',
            'comune.max' => 'Devi selezionare il comune',
            'cap_extra.string' => 'Devi inserire il CAP del tuo paese',
            'cap_extra.max' => 'Il cap non deve superare 8 caratteri',
            'city.string' => 'Devi inserire la città di residenza',
            'city.max' => 'La città inserita non può superare 30 caratteri',
            'state.string' => 'Controlla lo state che hai inserito',
            'state.max' => 'Lo state non può superare 30 caratteri',
            'codice_fiscale.string' => 'Il codice fiscale inserito non è corretto',
            'codice_fiscale.min' => 'Il codice fiscale inserito non è corretto',
            'codice_fiscale.max' => 'Il codice fiscale inserito non è corretto',
            'rea.required' => 'Devi inserire il numero d\'iscrizione alla C.C.I.A.A.',
            'rea.string' => 'Controlla l\'iscrizione alla C.C.I.A.A. che hai inserito',
            'rea.max' => 'Il numero d\'iscrizione alla C.C.I.A.A. non può superare 8 caratteri',
            'web.string' => 'Controlla l\'url della tua home page',
            'web.max' => 'Controlla l\'url della tua home page',
            'telefono.digits_between' => 'Inserisci solo le cifre numeriche del tuo recapito telefonico',
            'cellulare.digits_between' => 'Inserisci solo le cifre numeriche del tuo cellulare',
            'fax.digits_between' => 'Inserisci solo le cifre numeriche del tuo fax',
            'pec.email' => 'La pec inserita non ha il formato di un email',
            'pec.max' => 'La pec inserita non ha il formato di un email',
            'logo.image' => 'Il file caricato non è di tipo immagine'
        ];
    }
}

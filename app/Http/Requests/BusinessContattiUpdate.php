<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BusinessContattiUpdate extends FormRequest
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
            'web' => 'nullable|string|max:30',
            'telefono' => 'nullable|digits_between:5,16',
            'cellulare' => 'nullable|digits_between:5,16',
            'fax' => 'nullable|digits_between:5,16',
            'pec' => 'nullable|email|max:30|unique:business_profiles',
        ];
    }

    public function messages()
    {
        return [
            'web.string' => 'Controlla l\'url della tua home page',
            'web.max' => 'Controlla l\'url della tua home page',
            'telefono.digits_between' => 'Inserisci solo le cifre numeriche del tuo recapito telefonico',
            'cellulare.digits_between' => 'Inserisci solo le cifre numeriche del tuo cellulare',
            'fax.digits_between' => 'Inserisci solo le cifre numeriche del tuo fax',
            'pec.email' => 'La pec inserita non ha il formato di un email',
            'pec.max' => 'La pec inserita non ha il formato di un email',
            'pec.unique' => 'L\'indirizzo di posta certificata inserito è già presente nei nostri archivi',
        ];
    }
}

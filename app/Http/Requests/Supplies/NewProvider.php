<?php

namespace App\Http\Requests\Supplies;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\Employee;

class NewProvider extends FormRequest
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
            'rag_soc_provider' => 'required|string|max:50|min:3',
            'provider_cod' => 'required|string|max:10|',
            'iva_provider' => 'required|string|digits:11',
            'address_provider' => 'nullable|string|max:150',
            'telefono_provider' => 'nullable|digits_between:5,16',
            'email_provider' => 'required|string|email|max:100',
        ];
    }

    public function messages()
    {
        return [
            'rag_soc_provider.required' => 'La ragione sociale è obbligatoria',
            'rag_soc_provider.string' => 'La ragione sociale è obbligatoria',
            'rag_soc_provider.max' => 'La ragione sociale non può superare i 50 caratteri',
            'rag_soc_provider.min' => 'La ragione sociale inserita è strana',
            'provider_cod.required' => 'Il codice del fornitore è obbligatorio per il mapping dei prodotti',
            'provider_cod.string' => 'Il codice del fornitore è obbligatorio per il mapping dei prodotti',
            'provider_cod.max' => 'Il codice del fornitore non può superare i 10 caratteri',
            'iva_provider.required' => 'La partita iva è obbligatoria',
            'iva_provider.string' => 'La partita iva è obbligatoria',
            'iva_provider.digits' => 'La partita iva deve essere numerica con 11 cifre',
            'address_provider.string' => 'Controlla l\'indirizzo inserito',
            'adress_provider.max' => 'Hai inserito un indirizzo troppo lungo',
            'telefono_provider.digits_between' => 'Inserisci solo le cifre numeriche del telefono',
            'email_provider.required' => 'L\'email è obbligatoria',
            'email_provider.string' => 'Controlla l\'email inserita',
            'email_provider.email' => 'Controlla l\'email inserita',
            'email_provider.max' => 'Controlla l\'email inserita',
        ];
    }

}

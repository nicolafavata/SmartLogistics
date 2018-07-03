<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class NewEmployee extends FormRequest
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
            'name' => 'required|string|max:255|min:3',
            'cognome' => 'required|string|max:255|min:3',
            'matricola' => 'nullable|string|max:16',
            'tel_employee' => 'nullable|digits_between:5,16',
            'cell_employee' => 'nullable|digits_between:5,16',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'acquisti' => 'boolean',
            'produzione' => 'boolean',
            'vendite' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'email.required'=> 'Non hai inserito l\'email dell\'impiegato',
            'email.string' => 'Non hai inserito l\'email dell\'impiegato',
            'email.email' => 'Non hai inserito l\'email dell\'impiegato',
            'email.max' => 'Non hai inserito l\'email dell\'impiegato',
            'email.unique'  => 'L\'email inserita è già presente nei nostri archivi',
            'name.required' => 'Devi inserire il nome dell\'impiegato',
            'name.string' => 'Devi inserire il nome dell\'impiegato',
            'name.max' => 'Il nome inserito è troppo lungo',
            'name.min' => 'Il nome inserito è troppo breve',
            'cognome.required' => 'Devi inserire il cognome dell\'impiegato',
            'cognome.string' => 'Devi inserire il cognome del\'impiegato',
            'cognome.max' => 'Il cognome inserito è troppo lungo',
            'cognome.min' => 'Il cognome inserito è troppo breve',
            'matricola.string' => 'Il numero di matricola inserito non è valido',
            'matricola.max' => 'Il numero di matricola inserito è troppo lungo',
            'tel_employee.digits_between' => 'Inserisci solo le cifre numeriche del telefono dell\'impiegato',
            'cell_employee.digits_between' => 'Inserisci solo le cifre numeriche del mobile dell\'impiegato',
            'password.required' => 'Devi inserire una password di almeno 8 caratteri',
            'password.string' => 'Devi inserire una password di almeno 8 caratteri',
            'password.min' => 'Devi inserire una password di almeno 8 caratteri',
            'password.confirmed' => 'Devi confermare la password inserita'
        ];
    }


}

<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class UpEmployee extends FormRequest
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
            'acquisti' => 'boolean',
            'produzione' => 'boolean',
            'vendite' => 'boolean',
            'responsabile' =>'boolean'
        ];
    }

    public function messages()
    {
        return [
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
        ];
    }


}

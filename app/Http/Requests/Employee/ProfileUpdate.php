<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileUpdate extends FormRequest
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
        $email = DB::table('users')->select('email')->where('id',Auth::id())->first();
        return [
            'email' => 'required|string|email|max:255|unique:users,email,'.$email->email.',email',
            'tel_employee' => 'nullable|digits_between:5,16',
            'cell_employee' => 'nullable|digits_between:5,16',
        ];
    }

    public function messages()
    {
        return [
            'email.required'=> 'Non hai inserito l\'email',
            'email.string' => 'Non hai inserito l\'email',
            'email.email' => 'Non hai inserito l\'email',
            'email.max' => 'Non hai inserito l\'email',
            'email.unique'  => 'L\'email inserita è già presente nei nostri archivi',
            'tel_employee.digits_between' => 'Inserisci solo le cifre numeriche del telefono',
            'cell_employee.digits_between' => 'Inserisci solo le cifre numeriche del mobile',
        ];
    }
}

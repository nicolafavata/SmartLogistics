<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FindProduct extends FormRequest
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
            'product' => 'required|digits_between:5,18',
        ];
    }

    public function messages()
    {
        return [
            'product.required' => 'Non hai inserito il codice a barre',
            'product.digits_between' => 'Il codice a barre deve essere numerico con un massimo di 18 cifre',
        ];
    }
}

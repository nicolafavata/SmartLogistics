<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SettingExpire extends FormRequest
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
            'days_batchExpMon' => 'numeric',
            'warned' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'days_batchExpMon.numeric' => 'Devi inserire un numero per indicare i giorni di preavviso',
            'warned.boolean' => 'Controlla i dati inseriti'
        ];
    }
}

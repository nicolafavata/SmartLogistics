<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PictureUpdate extends FormRequest
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
            'img_employee' => 'required|image',
        ];
    }

    public function messages()
    {
        return [
            'img_employee.image' => 'Il file caricato non è di tipo immagine',
            'img_employee.required' => 'Non hai selezionato nessun file'
        ];
    }
}

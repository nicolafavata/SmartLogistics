<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadCatalogue extends FormRequest
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
            'catalogue' => 'required|file',
        ];
    }

    public function messages()
    {
        return [
            'catalogue.required' => 'Non hai caricato il file dei listini',
            'catalogue.file' => 'Non hai caricato un file valido per i listini'
        ];
    }
}

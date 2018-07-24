<?php

namespace App\Http\Requests\Supplies;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadExpires extends FormRequest
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
            'expires' => 'required|file',
        ];
    }

    public function messages()
    {
        return [
            'expires.required' => 'Non hai caricato il file delle scadenze',
            'expires.file' => 'Non hai caricato un file valido per le scadenze',
        ];
    }
}

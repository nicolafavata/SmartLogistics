<?php

namespace App\Http\Requests\Supplies;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadMapProvider extends FormRequest
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
            'mapping' => 'required|file',
        ];
    }

    public function messages()
    {
        return [
            'mapping.required' => 'Non hai caricato il file del mapping',
            'mapping.file' => 'Non hai caricato un file valido per il mapping',
        ];
    }
}

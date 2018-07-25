<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadProduction extends FormRequest
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
            'production' => 'required|file',
            'id_production' => 'nullable|exists:productions,id_production',
            'forecast' => 'nullable|boolean',
            'new' => 'nullable|boolean',
            'nothing' => 'nullable|boolean',

        ];
    }

    public function messages()
    {
        return [
            'production.required' => 'Non hai caricato il file dei prodotti',
            'production.file' => 'Non hai caricato un file valido per i prodotti',
            'id_production.exists' => 'Il prodotto selezionato non esiste',
        ];
    }
}

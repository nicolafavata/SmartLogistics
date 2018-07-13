<?php

namespace App\Http\Requests\Supplies;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadInventory extends FormRequest
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
            'inventory' => 'required|file',
            'file-historical' => 'nullable|file',
            'id_inventory' => 'nullable|exists:inventories,id_inventory',
            'historical' => 'nullable|boolean',
            'forecast' => 'nullable|boolean',
            'new' => 'nullable|boolean',

        ];
    }

    public function messages()
    {
        return [
            'inventory.required' => 'Non hai caricato il file dei prodotti',
            'inventory.file' => 'Non hai caricato un file valido per i prodotti',
            'file-historical.file' => 'Non hai caricato un file valido per i prodotti',
            'id_inventory.exists' => 'Il prodotto selezionato non esiste',
        ];
    }
}

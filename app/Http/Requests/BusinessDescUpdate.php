<?php

namespace App\Http\Requests;

use App\Models\BusinessProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessDescUpdate extends FormRequest
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
            'descrizione' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'descrizione.string' => 'Controlla la descrizione dell\'azienda',
            'descrizione.max' => 'La descrizione dell\'azienda non può superare i 255 caratteri',
        ];
    }
}

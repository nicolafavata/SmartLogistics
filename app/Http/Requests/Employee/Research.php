<?php

namespace App\Http\Requests\Employee;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class Research extends FormRequest
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
            'research' => 'required|string|digits:11|exists:company_offices,partita_iva_company'
        ];
    }

    public function messages()
    {
        return [
            'research.required' => 'Devi inserire la partita iva da ricercare',
            'research.string' => 'Devi inserire la partita iva da ricercare',
            'research.digits' => 'La partita iva deve avere 11 cifre numeriche',
            'research.exists' => 'La partita iva non esiste nei nostri archivi',
        ];
    }


}

<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CloseOrderSales extends FormRequest
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
            'document' => 'required',
            'date' => 'required|date',
            'number' => 'required|numeric',
            'type' => 'required|string',
            'customer' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'document.required' => 'Non hai selezionato gli ordini da chiudere',
            'date.required' => 'Devi inserire la data del documento',
            'date.date' => 'Devi inserire la data del documento',
            'number.required' => 'Devi inserire il numero del documento',
            'number.numeric' => 'Devi inserire il numero del documento',
            'type.required' => 'Devi selezionare il tipo di documento',
            'type.string' => 'Devi selezionare il tipo di documento',
            'customer.required' => 'Devi selezionare il cliente',
            'customer.string' => 'Devi selezionare il cliente',
        ];
    }
}

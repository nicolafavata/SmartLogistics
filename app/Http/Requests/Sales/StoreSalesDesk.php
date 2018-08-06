<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSalesDesk extends FormRequest
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
            'documentitems' => 'required',
            'desk_salesDeskCon' => 'required|exists:sales_desks,id_sales_desk',
            'number_sales_desk' => 'required|numeric',
            'date_sales_desk' => 'required|date'
        ];
    }

    public function messages()
    {
        return [
            'documentitems.required' => 'Non hai inserito prodotti nel documento',
            'desk_salesDeskCon.required' => 'Hai cancellato un\'attributo necessario, annullare il documento e riprovare',
            'desk_salesDeskCon.exists' => 'Hai cancellato un\'attributo necessario, annullare il documento e riprovare',
            'number_sales_desk.required' => 'Devi inserire il numero del documento',
            'number_sales_desk.numeric' => 'Devi inserire il numero del documento',
            'date_sales_desk.required' => 'Non hai inserito la data del documento',
            'date_sales_desk.date' => 'Non hai inserito la data del documento',
        ];
    }
}

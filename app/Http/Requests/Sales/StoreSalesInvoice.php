<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSalesInvoice extends FormRequest
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
        //    'invoice_salesInvCon' => 'required|exists:sales_invoices,id_sales_invoice',
            'number_sales_desk' => 'required|numeric',
            'date_sales_desk' => 'required|date',
            'desc_customer' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'documentitems.required' => 'Non hai inserito prodotti nel documento',
            'invoice_salesInvCon.required' => 'Hai cancellato un\'attributo necessario, annullare il documento e riprovare',
            'invoice_salesInvCon.exists' => 'Hai cancellato un\'attributo necessario, annullare il documento e riprovare',
            'number_sales_desk.required' => 'Devi inserire il numero del documento',
            'number_sales_desk.numeric' => 'Devi inserire il numero del documento',
            'date_sales_desk.required' => 'Non hai inserito la data del documento',
            'date_sales_desk.date' => 'Non hai inserito la data del documento',
            'desc_customer.required' => 'Devi inserire il riferimento al cliente'
        ];
    }
}

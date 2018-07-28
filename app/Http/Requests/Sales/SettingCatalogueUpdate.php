<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingCatalogueUpdate extends FormRequest
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
            'price_user' => 'numeric',
            'price_b2b' => 'numeric',
            'visible_sales_list' => 'boolean',
            'quantity_sales_list' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'price_user.numeric' => 'Il prezzo deve essere numerico',
            'price_b2b.numeric' => 'Il prezzo deve essere numerico',
            'visible_sales_list.boolean' => 'Ricontrolla la visibilità',
            'quantity_sales_list.boolean' => 'Ricontrolla la visibilità della disponibilità',
        ];
    }
}

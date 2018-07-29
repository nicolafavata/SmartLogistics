<?php

namespace App\Http\Requests\Supplies;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\Employee;

class SettingConfig extends FormRequest
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

            'provider_config_order' => 'required|exists:config_orders,provider_config_order',
            'lead_time_config' => 'required|numeric|between:0,365',
            'window_first_config' => 'required|numeric|between:1,31',
            'window_last_config' => 'required|numeric|between:1,31',
            'min_import_config' => 'required|numeric',
            'max_import_config' => 'required|numeric',
            'mapping_config' => 'required|string|size:2',
            'transmission_config' => 'required|boolean',
            'level_config' => 'nullable|boolean',
            'execute_config' => 'required|boolean',
            'days_number_config' => 'nullable|numeric|between:0,365',
        ];
    }

    public function messages()
    {
        return [
            'lead_time_config.required' => 'Non hai specificato il lead time',
            'lead_time_config.numeric' => 'Il lead time deve essere di tipo numerico',
            'lead_time_config.between' => 'Il lead time non può superare i 365 giorni',
            'window_first_config.required' => 'Non hai specificato il giorno iniziale della finestra temporale',
            'window_first_config.numeric' => 'La finestra temporale deve essere di tipo numerico',
            'window_first_config.between' => 'Il primo giorno di esecuzione degli ordini deve essere compreso tra 1 e 31',
            'window_last_config.required' => 'Non hai specificato il giorno finale della finestra temporale',
            'window_last_config.numeric' => 'La finestra temporale deve essere di tipo numerico',
            'window_last_config.between' => 'La finestra temporale di esecuzione deve essere compresa tra 1 e 31',
            'min_import_config.required' => 'Non hai specificato l\'importo minimo dell\'ordine',
            'min_import_config.numeric' => 'L\'importo minimo dell\'ordine deve essere di tipo numerico',
            'max_import_config.required' => 'Non hai specificato l\'importo massimo dell\'ordine',
            'max_import_config.numeric' => 'L\'importo massimo dell\'ordine deve essere di tipo numerico',
            'mapping_config.required' => 'Devi selezionare la tipologia di prodotti da ordinare',
            'mapping_config.string' => 'Devi selezionare la tipologia di prodotti da ordinare',
            'mapping_config.size' => 'Devi selezionare la tipologia di prodotti da ordinare',
            'transmission_config.required' => 'Devi selezionare la modalità di trasmissione dell\'ordine',
            'transmission_config.boolean' => 'Devi selezionare la modalità di trasmissione dell\'ordine',
            'level_config.boolean' => 'Controlla il flag sul livello di sicurezza',
            'execute_config.required' => 'Devi selezionare l\'evento di esecuzione dell\'ordine tra inizio del mese o numero di giorni',
            'execute_config.boolean' => 'Devi selezionare l\'evento di esecuzione dell\'ordine tra inizio del mese o numero di giorni',
            'days_number_config.numeric' => 'Il numero di giorni di esecuzione dell\'ordine deve essere di tipo numerico',
            'days_number_config.between' => 'I giorni di esecuzione dell\'ordine deve essere compreso tra 1 e 365'
        ];
    }

}

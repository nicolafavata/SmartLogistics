<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigOrder extends Model
{
    protected $primaryKey = 'id_config_order';

    protected $fillable = [
        'id_config_order','company_config_order','provider_config_order','lead_time_config','window_first_config','window_last_config','min_import_config','max_import_config','mapping_config','transmission_config','execute_config','days_number_config','level_config',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastHoltModel extends Model
{
    protected $primaryKey = 'id_forecast_holt_model';

    protected $fillable = [
        'id_forecast_holt_model','ForecastHoltProduct','alfa_holt','beta_holt','level_holt','trend_holt','initial_month_holt','1','2','3','4','5','6','7','8','9','10','11','12','error1','error2','error3','error4','error5','error6','error7','error8','error9','error10','error11','error12',
    ];
}

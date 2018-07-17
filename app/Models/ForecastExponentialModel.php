<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastExponentialModel extends Model
{
    protected $primaryKey = 'id_forecast_exponential_model';

    protected $fillable = [
        'id_forecast_exponential_model','ForecastExpoProduct','alfa_expo','level_expo','initial_month_expo','1','2','3','4','5','6','7','8','9','10','11','12','error1','error2','error3','error4','error5','error6','error7','error8','error9','error10','error11','error12',
    ];
}

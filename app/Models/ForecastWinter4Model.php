<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastWinter4Model extends Model
{
    protected $primaryKey = 'id_forecast_winter4_model';

    protected $fillable = [
        'id_forecast_winter4_model','Forecastwinter4Product','alfa_winter4','beta_winter4','gamma_winter4','level_winter4','trend_winter4','factor1','factor2','factor3','factor4','initial_month_winter4','1','2','3','4','5','6','7','8','error1','error2','error3','error4','error5','error6','error7','error8',
    ];
}

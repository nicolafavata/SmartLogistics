<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastWinter2Model extends Model
{
    protected $primaryKey = 'id_forecast_winter2_model';

    protected $fillable = [
        'id_forecast_winter2_model','Forecastwinter2Product','alfa_winter2','beta_winter2','gamma_winter2','level_winter2','trend_winter2','factor1_winter2','factor2_winter2','initial_month_winter2','1','2','3','4','error1','error2','error3','error4',
    ];
}

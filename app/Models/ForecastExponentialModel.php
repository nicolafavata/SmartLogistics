<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastExponentialModel extends Model
{
    protected $primaryKey = 'id_forecast_exponential_model';

    protected $fillable = [
        'id_forecast_exponential_model','ForecastExpoProduct','alfa_expo','level_expo','initial_month_expo','1','2','3','4','5','6','7','8','9','10','11','12',
    ];
}

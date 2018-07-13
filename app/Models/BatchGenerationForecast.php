<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchGenerationForecast extends Model
{
    protected $primaryKey = 'id_generation_forecast';

    protected $fillable = [
        'id_generation_forecast','GenerationForecast','GenerationForecastModel','booking_generation_forecast','executed_generation_forecast',
    ];
}

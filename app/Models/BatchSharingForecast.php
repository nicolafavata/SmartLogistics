<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchSharingForecast extends Model
{
    protected $primaryKey = 'id_sharing_forecast';

    protected $fillable = [
        'id_sharing_forecast','sharing_forecast','sharing_product','sharing_forecast_model','booking_sharing_forecast','executed_sharing_forecast',
    ];
}

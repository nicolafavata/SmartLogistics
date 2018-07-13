<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalData extends Model
{
    protected $primaryKey = 'id_historical_data';

    protected $fillable = [
        'id_historical_data','product_historical_data','1','2','3','4','5','6','7','8','9','10','11','12','initial_month',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchProcessParameter extends Model
{
    protected $primaryKey = 'id_process_parameter';

    protected $fillable = [
        'id_process_parameter','process_parameter','process_parameter_forecast_model','period','sales','booking_process_parameter','executed_process_parameter',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchRevisionParameter extends Model
{
    protected $primaryKey = 'id_revision_parameter';

    protected $fillable = [
        'id_revision_parameter','revision_parameter','revision_parameter_forecast_model','booking_revision_parameter','executed_revision_parameter',
    ];
}

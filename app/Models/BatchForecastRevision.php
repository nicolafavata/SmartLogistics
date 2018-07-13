<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchForecastRevision extends Model
{
    protected $primaryKey = 'id_forecast_revision';

    protected $fillable = [
        'id_forecast_revision','forecast_revision','RevisionForecastModel','booking_revision_forecast','executed_revision_forecast',
    ];
}

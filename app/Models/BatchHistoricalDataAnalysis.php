<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchHistoricalDataAnalysis extends Model
{
    protected $primaryKey = 'id_batch_historical_data_analysi';

    protected $fillable = [
        'id_batch_historical_data_analysi','CompanyDataAnalysis','productDataAnalysis','booking_historical_data_analysi','executed',
    ];
}

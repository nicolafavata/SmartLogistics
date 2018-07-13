<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchHistoricalData extends Model
{
    protected $primaryKey = 'id_batchHisDat';

    protected $fillable = [
        'id_batchHisDat','company_batchHisDat','url_batchHisDat','email_batchHisDat','executed_batchHisDat',
    ];
}

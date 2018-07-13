<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_monitoringOrder extends Model
{
    protected $primaryKey = 'id_batch_monitoring_order';

    protected $fillable = [
        'id_batch_monitoring_order','company_batchMonOrder','configOrder_batchMonOrder','limit_day_batch_monitoring_order','window_first_batch_monitoring_order','windows_last_batch_monitoring_order','date_batch_monitoring_order',
    ];
}

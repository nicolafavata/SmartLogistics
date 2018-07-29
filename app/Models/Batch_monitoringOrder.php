<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_monitoringOrder extends Model
{
    protected $primaryKey = 'id_batch_monitoring_order';

    protected $fillable = [
        'id_batch_monitoring_order','company_batchMonOrder','configOrder_batchMonOrder','email_monitoring_order','limit_day_batch_monitoring_order','first_day_batch_monitoring_order','level_control','date_batch_monitoring_order',
    ];
}

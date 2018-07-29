<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchExpiriesMonitoring extends Model
{
    protected $primaryKey = 'id_batchExpMon';

    protected $fillable = [
        'id_batchExpMon','days_batchExpMon','employee_batchExpMon','email_batch_exp_mon','warned',
    ];
}

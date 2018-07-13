<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_Expiry extends Model
{
    protected $table = 'batch_expiries';

    protected $primaryKey = 'id_batch_expiries';

    protected $fillable = [
        'id_batch_expiries','company_batch_expiries','url_file_batch_expiries','email_batch_expiries','executed_batch_expiries',
    ];
}

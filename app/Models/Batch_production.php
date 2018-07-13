<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_production extends Model
{

    protected $primaryKey = 'id_batch_production';

    protected $fillable = [
        'id_batch_production','company_batch_production','url_file_batch_production','email_batch_production','executed_batch_production',
    ];
}

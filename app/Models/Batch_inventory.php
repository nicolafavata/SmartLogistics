<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_inventory extends Model
{
    protected $primaryKey = 'id_batch_inventory';

    protected $fillable = [
        'id_batch_inventory','company_batch_inventory','url_file_batch_inventory','initial','email_batch_inventory','executed_batch_inventory',
    ];
}

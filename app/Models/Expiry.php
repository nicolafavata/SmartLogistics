<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expiry extends Model
{
    protected $primaryKey = 'id_expiry';

    protected $fillable = [
        'id_expiry','company_expiry','inventory_expiry','stock_expiry','date_expiry',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyRequest extends Model
{
    protected $primaryKey = 'id_supply_request';

    protected $fillable = [
        'id_supply_request','block','supply','company_requested','company_received','recipient',
    ];
}

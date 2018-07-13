<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyChain extends Model
{
    protected $primaryKey = 'id_supply_chain';

    protected $fillable = [
        'id_supply_chain','company_supply_shares','company_supply_received','forecast','availability','b2b','ean_mapping',
    ];
}

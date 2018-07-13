<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingInventoryProduction extends Model
{
    protected $primaryKey = 'id_mapping_inventory_production';

    protected $fillable = [
        'id_mapping_inventory_production','company_mapping_production','inventory_map_pro','production_map_pro','quantity_mapping_production',
    ];
}

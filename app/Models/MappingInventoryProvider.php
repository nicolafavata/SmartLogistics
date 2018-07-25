<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingInventoryProvider extends Model
{
    protected $primaryKey = 'id_mapping_inventory_provider';

    protected $fillable = [
        'id_mapping_inventory_provider','company_mapping_provider','price_provider','first','inventory_mapping_provider','provider_mapping_provider','ean_mapping_inventory_provider','cod_mapping_inventory_provider',
    ];
}

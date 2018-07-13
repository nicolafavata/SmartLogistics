<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_MappingInventoryProvider extends Model
{
    protected $table = 'batch_mappingInventoryProviders';

    protected $primaryKey = 'id_batch_mapping_inventory_provider';

    protected $fillable = [
        'id_batch_mapping_inventory_provider','company_batchMapPro','url_file_batch_mapping_provider','email_batch_mapping_provider','executed_batch_mapping_provider','provider_batchMapPro',
    ];
}

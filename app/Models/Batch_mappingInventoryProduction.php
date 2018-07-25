<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch_mappingInventoryProduction extends Model
{
    protected $table = 'batch_mapping_productions';

    protected $primaryKey = 'id_batch_mapping_production';

    protected $fillable = [
        'id_batch_mapping_production','company_batch_map_pro','url_file_batch_map_pro','email_batch_map_pro','executed_batch_map_pro',
    ];
}

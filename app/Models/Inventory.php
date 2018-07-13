<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $primaryKey = 'id_inventory';

    protected $fillable = [
        'id_inventory','company_inventory','cod_inventory','title_inventory','category_first','category_second','unit_inventory','stock','committed','arriving','url_inventory','description_inventory','brand','ean_inventory','average_cost_inventory','last_cost_inventory','codice_iva_inventory','imposta_inventory','imposta_desc_inventory','height_inventory','width_inventory','depth_inventory','weight_inventory','expire_inventory','sale_inventory',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $primaryKey = 'id_production';

    protected $fillable = [
        'id_production','company_production','cod_production','title_production','category_first_production','category_second_production','unit_production','url_production','description_production','brand_production','ean_production','codice_iva_production','imposta_production','imposta_desc_production','height_production','width_production','depth_production','weight_production',
    ];
}

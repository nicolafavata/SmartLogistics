<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $primaryKey = 'id_provider';

    protected $fillable = [
        'id_provider','company_provider','supply_provider','provider_supply','provider_cod','rag_soc_provider','iva_provider','address_provider','telefono_provider','email_provider',
    ];
}

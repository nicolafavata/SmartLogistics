<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRegistryExtra extends Model
{
    protected $primaryKey = 'id_customer_registry_extra';

    protected $fillable = [
        'id_customer_registry_extra','cap_customer_extra','city_customer_extra','state_customer_extra','customer_registry',
    ];
}

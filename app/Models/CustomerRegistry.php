<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRegistry extends Model
{
    protected $primaryKey = 'id_customer_registry';

    protected $fillable = [
        'id_customer_registry','company_customer_registry','user_customer_registry','office_customer_registry','rag_soc_customer','address_customer','cap_customer','telefono_customer','email_customer',
    ];
}

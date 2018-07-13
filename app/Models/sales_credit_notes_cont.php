<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sales_credit_notes_cont extends Model
{
    protected $primaryKey = 'id_salesCrNoCo';

    protected $fillable = [
        'id_salesCrNoCo','creditNote','product_salesCrNoCo','quantity_salesCrNoCo','discount_salesCrNoCo',
    ];
}

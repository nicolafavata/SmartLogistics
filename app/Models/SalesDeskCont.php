<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDeskCont extends Model
{
    protected $primaryKey = 'sales_desk_conts';

    protected $fillable = [
        'sales_desk_conts','desk_salesDeskCon','product_salesDeskCon','quantity_salesDeskCon','discount_salesDeskCon','imposta_salesDeskCon','price_salesDeskCon'
    ];
}

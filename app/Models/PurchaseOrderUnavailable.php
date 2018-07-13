<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderUnavailable extends Model
{
    protected $primaryKey = 'id_purchase_order_unavailable';

    protected $fillable = [
        'id_purchase_order_unavailable','order_unav','inventory_unav','quantity_purchase_unavailable','unit_price_purchase_unavailable',
    ];
}

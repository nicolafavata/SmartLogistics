<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderContent extends Model
{
    protected $primaryKey = 'id_purchase_order_content';

    protected $fillable = [
        'id_purchase_order_content','order_purchase_content','inventory_purchase_content','quantity_purchase_content','unit_price_purchase_content','discount','expiry_purchase_content'
    ];
}

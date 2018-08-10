<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $primaryKey = 'id_purchase_order';

    protected $fillable = [
        'id_purchase_order','company_purchase_order','provider_purchase_order','order_number_purchase','order_date_purchase','state_purchase_order','comment_purchase_order','iva_purchase_order','total_purchase_order','reference_purchase_order',
    ];
}

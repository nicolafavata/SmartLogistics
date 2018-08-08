<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class salesCustomerOrdersCont extends Model
{
    protected $primaryKey = 'id_sales_customer_orders_cont';

    protected $fillable = [
        'id_sales_customer_orders_cont','customer_order','product_salesCustOrdCon','imposta_customer_orders_cont','price_customer_orders_cont','quantity_sales_customer_orders_cont','discount_sales_customer_orders_cont',
    ];
}

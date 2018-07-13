<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDdtCont extends Model
{
    protected $primaryKey = 'id_sales_ddt_cont';

    protected $fillable = [
        'id_sales_ddt_cont','ddt_salesDdtCon','product_salesDdtCon','quantity_sales_ddt_cont','discount_sales_ddt_cont',
    ];
}

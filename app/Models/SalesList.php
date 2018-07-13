<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesList extends Model
{
    protected $primaryKey = 'id_sales_list';

    protected $fillable = [
        'id_sales_list','company_sales_list','inventory_sales_list','production_sales_list','visible_sales_list','price_user','price_b2b','quantity_sales_list','forecast_model',
    ];
}

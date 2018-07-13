<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDdt extends Model
{
    protected $primaryKey = 'id_sales_ddts';

    protected $fillable = [
        'id_sales_ddts','company_sales_ddts','customer_sales_ddts','customer-company_sales_ddts','number_sales_ddts','date_sales_ddts','total_sales_ddts','state_sales_ddts','reference_sales_ddts',
    ];
}

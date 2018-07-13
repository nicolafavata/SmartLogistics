<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDesk extends Model
{
    protected $primaryKey = 'id_sales_desk';

    protected $fillable = [
        'id_sales_desk','company_sales_desk','customer_sales_desk','customer-company_sales_desk','number_sales_desk','date_sales_desk','total_sales_desk',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesCreditNote extends Model
{
    protected $primaryKey = 'id_sales_credit_note';

    protected $fillable = [
        'id_sales_credit_note','company_sales_credit_note','customer_sales_credit_note','customer-company-sales_credit_notee','number_sales_credit_note','date_sales_credit_note','total_sales_credit_note',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $primaryKey = 'id_sales_invoice';

    protected $fillable = [
        'id_sales_invoice','company_sales_invoice','customer_sales_invoice','customer-company-sales_invoice','customer_reference_invoice','number_sales_invoice','date_sales_invoice','total_sales_invoice',
    ];
}

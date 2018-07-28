<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchSalesList extends Model
{
    protected $primaryKey = 'id_batch_sales_list';

    protected $fillable = [
        'id_batch_sales_list','company_batch_sales_list','url_file_batch_sales_list','email_batch_sales_list','executed_batch_sales_list',
    ];
}

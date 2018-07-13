<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceCont extends Model
{
    protected $primaryKey = 'id_salesInvCon';

    protected $fillable = [
        'id_salesInvCon','invoice_salesInvCon','product_salesInvCon','quantity_salesInvCon','discount_salesInvCon',
    ];
}

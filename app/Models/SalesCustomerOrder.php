<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesCustomerOrder extends Model
{
    protected $primaryKey = 'id_salesCust-or';

    protected $fillable = [
        'id_salesCust-or','company_salesCust-or','customer_salesCust-or','cust-com-salesCust-or','number_salesCust-or','date_salesCust-or','total_salesCust-or','state_salesCust-or','reference_salesCust-or','ref-desk_salesCust-or',
    ];
}

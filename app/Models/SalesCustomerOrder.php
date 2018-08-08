<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesCustomerOrder extends Model
{
    protected $primaryKey = 'id_salesCust_or';

    protected $fillable = [
        'id_salesCust_or','company_salesCust_or','customer_salesCust_or','cust-com-salesCust_or','number_salesCust_or','date_salesCust_or','total_salesCust_or','customer_reference_order','state_salesCust_or','reference_salesCust_or','ref-desk_salesCust_or',
    ];
}

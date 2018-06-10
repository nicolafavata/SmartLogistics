<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyOfficeExtraItalia extends Model
{
    protected $table = 'company_offices_extra_italia';

    protected $primaryKey = 'id_company_office_extra';

    protected $fillable = [
        'id_company_office_extra','cap_company_office_extra','city_company_office_extra','state_company_office_extra','company_office',
    ];
}

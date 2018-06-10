<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCategorie extends Model
{
    protected $primaryKey = 'id_company_categoria';

    protected $fillable = [
        'id_company_categoria', 'company' , 'categoria',
    ];
}

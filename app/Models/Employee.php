<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'id_employee';

    protected $fillable = [
        'id_employee', 'matricola', 'user_employee', 'company_employee', 'tel_employee','cell_employee','img_employee','responsabile','acquisti','produzione','vendite',
    ];
}

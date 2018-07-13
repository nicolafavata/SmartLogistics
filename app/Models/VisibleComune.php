<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisibleComune extends Model
{
    protected $table = 'visible_comuni';

    protected $primaryKey = 'id_visible_comune';

    protected $fillable = [
        'id_visible_comune','cap_visible','company_office_visible',
    ];
}

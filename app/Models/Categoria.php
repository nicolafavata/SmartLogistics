<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorie';

    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'categoria',
    ];
}

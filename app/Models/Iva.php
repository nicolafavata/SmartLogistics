<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iva extends Model
{
    protected $table = 'iva';

    protected $primaryKey = 'codice_iva';

    protected $fillable = [
        'codice_iva', 'imposta', 'indetr', 'classe_iva' , 'descrizione', 'note' ,
    ];
}

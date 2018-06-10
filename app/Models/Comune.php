<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comune extends Model {

    protected $table = 'comuni';

    protected $primaryKey = 'id_comune';

    protected $fillable = [
        'id_comune', 'categoria',
    ];
}
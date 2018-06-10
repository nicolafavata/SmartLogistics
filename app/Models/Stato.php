<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stato extends Model {

    protected $table = 'stati';

    protected $primaryKey = 'id_stato';

    protected $fillable = [
        'id_stato', 'nome_stato', 'sigla_numerica_stato', 'sigla_iso_3166_1_alpha_3_stato', 'sigla_iso_3166_1_alpha_2_stato',
    ];
}
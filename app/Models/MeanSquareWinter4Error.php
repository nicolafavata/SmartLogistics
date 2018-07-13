<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeanSquareWinter4Error extends Model
{
    protected $primaryKey = 'id_mean_square_winter4';

    protected $fillable = [
        'id_mean_square_winter4','mean_square_winter4','alfa_mean_square_winter4','beta_mean_square_winter4','gamma_mean_square_winter4','level_mean_square_winter4','trend_mean_square_winter4','factor1_mean_square_winter4','factor2_mean_square_winter4','factor3_mean_square_winter4','factor4_mean_square_winter4','month_mean_square_winter4','mean_square_winter4_error',
    ];
}

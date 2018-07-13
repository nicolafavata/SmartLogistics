<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeanSquareWinter2Error extends Model
{
    protected $primaryKey = 'id_mean_square_winter2';

    protected $fillable = [
        'id_mean_square_winter2','mean_square_winter2','alfa_mean_square_winter2','beta_mean_square_winter2','gamma_mean_square_winter2','level_mean_square_winter2','trend_mean_square_winter2','factor1_mean_square_winter2','factor2_mean_square_winter2','month_mean_square_winter2','mean_square_winter2_error',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeanSquareHoltError extends Model
{
    protected $primaryKey = 'id_mean_square_holt';

    protected $fillable = [
        'id_mean_square_holt','mean_square_holt','alfa_mean_square_holt','beta_mean_square_holt','level_mean_square_holt','trend_mean_square_holt','month_mean_square_holt','mean_square_holt_error',
    ];

}

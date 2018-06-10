<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersProfileExtraItalia extends Model
{
    protected $table = 'users_profile_extra_italia';

    protected $primaryKey = 'id_user_profile_extra_italia';

    protected $fillable = [
        'id_user_profile_extra_italia','cap_user_profile_extra_italia','city_user_profile_extra_italia','state_user_profile_extra_italia','user_extra_italia',
    ];
}

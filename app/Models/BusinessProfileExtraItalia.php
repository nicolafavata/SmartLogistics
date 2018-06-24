<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfileExtraItalia extends Model
{
    protected $table = 'business_profiles_extra_italia';

    protected $primaryKey = 'id_business_profile_extra';

    protected $fillable = [
        'id_business_profile_extra', 'cap_extra', 'city','state','profilo',
    ];
}

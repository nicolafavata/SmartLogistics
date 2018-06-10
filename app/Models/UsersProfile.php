<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersProfile extends Model
{
    protected $table = 'users_profiles';

    protected $primaryKey = 'id_user_profile';

    protected $fillable = [
        'id_user_profile','nazione_user_profile','indirizzo_user_profile','civico_user_profile','cap_user_profile','partita_iva_user_profile','codice_fiscale_user_profile','telefono_user_profile','cellulare_user_profile','img_user_profile','id_user',
    ];
}

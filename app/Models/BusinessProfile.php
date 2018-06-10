<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $table = 'business_profiless';

    protected $primaryKey = 'id_business_profile';

    protected $fillable = [
        'id_business_profile', 'rag_soc', 'descrizione','nazione','indirizzo','civico','cap','partita_iva','codice_fiscale','rea','web','telefono','cellulare','fax','pec','logo','id_admin',
    ];
}

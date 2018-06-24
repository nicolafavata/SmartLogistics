<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $table = 'business_profiles';

    protected $primaryKey = 'id_business_profile';

    protected $fillable = [
        'id_business_profile', 'rag_soc', 'descrizione','nazione','indirizzo','civico','cap_busines','partita_iva','codice_fiscale','rea','web','telefono','cellulare','fax','pec','logo','id_admin',
    ];

    public function getPathAttribute(){
        $url = $this->logo;
        if (stristr($this->logo,'http') === false){
            $url= 'storage/'.$this->logo;
        }
        return $url;
    }

}

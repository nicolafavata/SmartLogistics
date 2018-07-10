<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyOffice extends Model
{
    protected $table = 'company_offices';

    protected $primaryKey = 'id_company_office';

    protected $fillable = [
        'id_company_office','rag_soc_company','nazione_company', 'indirizzo_company','civico_company','cap_company','partita_iva_company','codice_fiscale_company','telefono_company','cellulare_company','fax_company','email_company','visible_user','visible_business','id_admin_company',
    ];
}

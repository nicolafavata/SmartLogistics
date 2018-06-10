<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_offices', function (Blueprint $table) {
            $table->increments('id_company_office');
            $table->string('rag_soc_company',50);
            $table->string('nazione_company',128);
            $table->string('indirizzo_company',30);
            $table->char('civico_company',6);
            $table->integer('cap_company');
            //Una sede ha un solo cap, un cap può avere più sedi
            $table->foreign('cap_company')->on('comuni')->references('id_comune')->onDelete('cascade')->onUpdate('cascade');
            $table->char('partita_iva_company',11);
            $table->char('codice_fiscale_company',16)->nullable();
            $table->string('telefono_company',16)->nullable();
            $table->string('cellulare_company',16)->nullable();
            $table->string('fax_company',16)->nullable();
            $table->string('email_company',30)->nullable();
            $table->integer('id_admin_company')->unsigned();
            //Una sede ha un solo amministratore, e un amministratore può avere più sedi
            $table->foreign('id_admin_company')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_offices');
    }
}

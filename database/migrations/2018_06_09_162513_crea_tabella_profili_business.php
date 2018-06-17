<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreaTabellaProfiliBusiness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->increments('id_business_profile');
            $table->string('rag_soc',50)->nullable();
            $table->mediumText('descrizione')->nullable();
            $table->string('nazione',128)->nullable();
            $table->string('indirizzo',30)->nullable();
            $table->char('civico',6)->nullable();
            $table->integer('cap')->nullable();
            //Un profilo ha un solo cap, un cap può avere più profili
            $table->foreign('cap')->on('comuni')->references('id_comune');
            $table->char('partita_iva',11)->unique();
            $table->char('codice_fiscale',16)->nullable();
            $table->char('rea',8)->nullable();
            $table->string('web',30)->nullable();
            $table->string('telefono',16)->nullable();
            $table->string('cellulare',16)->nullable();
            $table->string('fax',16)->nullable();
            $table->string('pec',30)->nullable();
            $table->string('logo',128)->nullable();
            $table->integer('id_admin')->unsigned();
            //Un profilo ha un solo amministratore, e un amministratore può avere un solo profilo
            $table->foreign('id_admin')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('business_profiles');
    }
}

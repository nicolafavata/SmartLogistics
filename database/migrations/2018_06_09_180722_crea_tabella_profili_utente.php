<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreaTabellaProfiliUtente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_profiles', function (Blueprint $table) {
            $table->increments('id_user_profile');
            $table->string('nazione_user_profile',128);
            $table->string('indirizzo_user_profile',30);
            $table->char('civico_user_profile',6);
            $table->integer('cap_user_profile');
            //Un profilo utente ha un solo cap, un cap può avere più profili utente
            $table->foreign('cap_user_profile')->on('comuni')->references('id_comune')->onDelete('cascade')->onUpdate('cascade');
            $table->char('partita_iva_user_profile',11)->nullable();
            $table->char('codice_fiscale_user_profile',16)->nullable();
            $table->string('telefono_user_profile',16)->nullable();
            $table->string('cellulare_user_profile',16)->nullable();
            $table->string('img_user_profile',128)->nullable();
            $table->integer('id_user')->unsigned();
            //Relazione uno a uno fra un utente e un profilo
            $table->foreign('id_user')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('users_profile');
    }
}

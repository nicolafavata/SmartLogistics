<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreaTabellaUtentiProfiliExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_profiles_extra_italia', function (Blueprint $table) {
            $table->increments('id_user_profile_extra_italia');
            $table->string('cap_user_profile_extra_italia',8);
            $table->string('city_user_profile_extra_italia',30);
            $table->string('state_user_profile_extra_italia',30)->nullable();
            $table->integer('user_extra_italia')->unsigned();
            //E' una relazione uno a uno tra un profilo e un indirizzo extra italia
            $table->foreign('user_extra_italia')->on('users_profiles')->references('id_user_profile')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('users_profiles_extra_italia');
    }
}

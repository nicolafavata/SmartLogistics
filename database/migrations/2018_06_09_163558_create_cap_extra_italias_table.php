<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapExtraItaliasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_profiles_extra_italia', function (Blueprint $table) {
            $table->increments('id_business_profile_extra');
            $table->string('cap_extra',8)->nullable();
            $table->string('city',30)->nullable();
            $table->string('state',30)->nullable();
            $table->integer('profilo')->unsigned();
            //E' una relazione uno a uno tra un profilo e un indirizzo extra italia
            $table->foreign('profilo')->on('business_profiles')->references('id_business_profile')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('cap_extra_italias');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerRegistryExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_registry_extras', function (Blueprint $table) {
            $table->increments('id_customer_registry_extra');
            $table->string('cap_customer_extra',8)->nullable();
            $table->string('city_customer_extra',30)->nullable();
            $table->string('state_customer_extra',30)->nullable();
            $table->integer('customer_registry')->unsigned();
            $table->foreign('customer_registry')->on('customer_registries')->references('id_customer_registry')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('customer_registry_extras');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplyRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_requests', function (Blueprint $table) {
            $table->increments('id_supply_request');
            $table->boolean('block')->default('0');
            $table->boolean('supply')->default('0');
            //Sede che ha richiesto l'aggregazione
            $table->integer('company_requested')->unsigned()->nullable();
            //E' una relazione uno a uno tra due sedi
            $table->foreign('company_requested')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            //Sede che ha ricevuto l'aggregazione
            $table->integer('company_received')->unsigned()->nullable();
            $table->foreign('company_received')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('supply_requests');
    }
}

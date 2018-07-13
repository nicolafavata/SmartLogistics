<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplyChainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_chains', function (Blueprint $table) {
            $table->increments('id_supply_chain');
            //La prima sede condivide le informazioni
            $table->integer('company_supply_shares')->unsigned()->nullable();
            //E' una relazione uno a uno tra due sedi
            $table->foreign('company_supply_shares')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            //La seconda sede riceve le informazioni condivise
            $table->integer('company_supply_received')->unsigned()->nullable();
            $table->foreign('company_supply_received')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('forecast')->default('0');
            $table->boolean('availability')->default('0');
            $table->boolean('b2b')->default('0');
            $table->boolean('ean_mapping')->default('0');
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
        Schema::dropIfExists('supply_chains');
    }
}

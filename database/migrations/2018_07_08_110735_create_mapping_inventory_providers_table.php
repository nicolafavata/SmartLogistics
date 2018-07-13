<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMappingInventoryProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapping_inventory_providers', function (Blueprint $table) {
            $table->increments('id_mapping_inventory_provider');
            $table->integer('company_mapping_provider')->unsigned();
            //Relazione sede con scadenza di un prodotto
            $table->foreign('company_mapping_provider')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('inventory_mapping_provider')->unsigned();
            //Relazione prodotto con scadenza
            $table->foreign('inventory_mapping_provider')->on('inventories')->references('id_inventory')->onDelete('cascade')->onUpdate('cascade');
            //Relazione con il fornitore
            $table->integer('provider_mapping_provider')->unsigned();
            //Relazione fornitore
            $table->foreign('provider_mapping_provider')->on('providers')->references('id_provider')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('ean_mapping_inventory_provider')->default('0');
            $table->string('cod_mapping_inventory_provider',20)->nullable();
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
        Schema::dropIfExists('mapping_inventory_providers');
    }
}

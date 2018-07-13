<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchMappingInventoryProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_mappingInventoryProviders', function (Blueprint $table) {
            $table->increments('id_batch_mapping_inventory_provider');
            $table->integer('company_batchMapPro')->unsigned();
            //Relazione sede con prenotazioni per elaborazione inventario
            $table->foreign('company_batchMapPro')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url_file_batch_mapping_provider',128)->default('0');
            $table->string('email_batch_mapping_provider')->nullable();
            $table->boolean('executed_batch_mapping_provider')->default('0');
            $table->integer('provider_batchMapPro')->unsigned();
            //Relazione fornitore
            $table->foreign('provider_batchMapPro')->on('providers')->references('id_provider')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('batch__mapping_inventory_providers');
    }
}

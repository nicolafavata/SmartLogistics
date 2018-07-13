<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMappingInventoryProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapping_inventory_productions', function (Blueprint $table) {
            $table->increments('id_mapping_inventory_production');
            $table->integer('company_mapping_production')->unsigned();
            $table->foreign('company_mapping_production')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('inventory_map_pro')->unsigned();
            $table->foreign('inventory_map_pro')->on('inventories')->references('id_inventory')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('production_map_pro')->unsigned();
            $table->foreign('production_map_pro')->on('productions')->references('id_production')->onDelete('cascade')->onUpdate('cascade');
            $table->double('quantity_mapping_production')->default(0);
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
        Schema::dropIfExists('mapping_inventory_productions');
    }
}

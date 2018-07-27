<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_lists', function (Blueprint $table) {
            $table->increments('id_sales_list');
            $table->integer('company_sales_list')->unsigned();
            $table->foreign('company_sales_list')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('inventory_sales_list')->unsigned()->nullable();
            $table->foreign('inventory_sales_list')->on('inventories')->references('id_inventory')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('production_sales_list')->unsigned()->nullable();
            $table->foreign('production_sales_list')->on('productions')->references('id_production')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('visible_sales_list')->default('0');
            $table->double('price_user')->default('0');
            $table->double('price_b2b')->default('0');
            $table->boolean('quantity_sales_list')->default('0');
            $table->string('forecast_model',2)->nullable();
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
        Schema::dropIfExists('sales_lists');
    }
}

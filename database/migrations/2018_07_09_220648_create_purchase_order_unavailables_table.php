<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderUnavailablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_unavailables', function (Blueprint $table) {
            $table->increments('id_purchase_order_unavailable');
            $table->integer('order_unav')->unsigned();
            $table->foreign('order_unav')->on('purchase_orders')->references('id_purchase_order')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('inventory_unav')->unsigned();
            $table->foreign('inventory_unav')->on('inventories')->references('id_inventory')->onDelete('cascade')->onUpdate('cascade');
            $table->double('quantity_purchase_unavailable')->default(0);
            $table->double('unit_price_purchase_unavailable')->default(0);
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
        Schema::dropIfExists('purchase_order_unavailables');
    }
}

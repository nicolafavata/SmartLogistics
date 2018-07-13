<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_contents', function (Blueprint $table) {
            $table->increments('id_purchase_order_content');
            $table->integer('order_purchase_content')->unsigned();
            $table->foreign('order_purchase_content')->on('purchase_orders')->references('id_purchase_order')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('inventory_purchase_content')->unsigned();
            $table->foreign('inventory_purchase_content')->on('inventories')->references('id_inventory')->onDelete('cascade')->onUpdate('cascade');
            $table->double('quantity_purchase_content')->default(0);
            $table->double('unit_price_purchase_content')->default(0);
            $table->double('discount')->default(0);
            $table->dateTime('expiry_purchase_content')->nullable();
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
        Schema::dropIfExists('purchase_order_contents');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesCustomerOrdersContsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_customer_orders_conts', function (Blueprint $table) {
            $table->increments('id_sales_customer_orders_cont');
            $table->integer('customer_order')->unsigned();
            $table->foreign('customer_order')->on('sales_customer_orders')->references('id_salesCust-or')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('product_salesCustOrdCon')->unsigned();
            $table->foreign('product_salesCustOrdCon')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity_sales_customer_orders_cont')->nullable();
            $table->double('discount_sales_customer_orders_cont')->default('0');
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
        Schema::dropIfExists('sales_customer_orders_conts');
    }
}

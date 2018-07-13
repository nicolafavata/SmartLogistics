<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDdtContsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_ddt_conts', function (Blueprint $table) {
            $table->increments('id_sales_ddt_cont');
            $table->integer('ddt_salesDdtCon')->unsigned();
            $table->foreign('ddt_salesDdtCon')->on('sales_ddts')->references('id_sales_ddts')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('product_salesDdtCon')->unsigned();
            $table->foreign('product_salesDdtCon')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity_sales_ddt_cont')->nullable();
            $table->double('discount_sales_ddt_cont')->default('0');
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
        Schema::dropIfExists('sales_ddt_conts');
    }
}

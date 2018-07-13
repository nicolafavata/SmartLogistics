<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDeskContsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_desk_conts', function (Blueprint $table) {
            $table->increments('id_salesDeskCon');
            $table->integer('desk_salesDeskCon')->unsigned();
            $table->foreign('desk_salesDeskCon')->on('sales_desks')->references('id_sales_desk')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('product_salesDeskCon')->unsigned();
            $table->foreign('product_salesDeskCon')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity_salesDeskCon')->nullable();
            $table->double('discount_salesDeskCon')->default('0');
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
        Schema::dropIfExists('sales_desk_conts');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricalDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_datas', function (Blueprint $table) {
            $table->increments('id_historical_data');
            $table->integer('product_historical_data')->unsigned();
            $table->foreign('product_historical_data')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->double('1')->default('0');
            $table->double('2')->default('0');
            $table->double('3')->default('0');
            $table->double('4')->default('0');
            $table->double('5')->default('0');
            $table->double('6')->default('0');
            $table->double('7')->default('0');
            $table->double('8')->default('0');
            $table->double('9')->default('0');
            $table->double('10')->default('0');
            $table->double('11')->default('0');
            $table->double('12')->default('0');
            $table->integer('initial_month')->default('1');
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
        Schema::dropIfExists('historical_datas');
    }
}

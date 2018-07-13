<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForecastHoltModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forecast_holt_models', function (Blueprint $table) {
            $table->increments('id_forecast_holt_model');
            $table->integer('ForecastHoltProduct')->unsigned();
            $table->foreign('ForecastHoltProduct')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->double('alfa_holt')->default(0.2);
            $table->double('beta_holt')->default(0.2);
            $table->double('level_holt')->default(0);
            $table->double('trend_holt')->default(1);
            $table->integer('initial_month_holt')->default(1);
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
        Schema::dropIfExists('forecast_holt_models');
    }
}

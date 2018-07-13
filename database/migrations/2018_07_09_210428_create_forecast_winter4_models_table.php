<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForecastWinter4ModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forecast_winter4_models', function (Blueprint $table) {
            $table->increments('id_forecast_winter4_model');
            $table->integer('Forecastwinter4Product')->unsigned();
            $table->foreign('Forecastwinter4Product')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->double('alfa_winter4')->default(0.2);
            $table->double('beta_winter4')->default(0.2);
            $table->double('gamma_winter4')->default(0.2);
            $table->double('level_winter4')->default(0);
            $table->double('trend_winter4')->default(1);
            $table->double('factor1')->default(1);
            $table->double('factor2')->default(1);
            $table->double('factor3')->default(1);
            $table->double('factor4')->default(1);
            $table->integer('initial_month_winter4')->default(1);
            $table->double('1')->default('0');
            $table->double('2')->default('0');
            $table->double('3')->default('0');
            $table->double('4')->default('0');
            $table->double('5')->default('0');
            $table->double('6')->default('0');
            $table->double('7')->default('0');
            $table->double('8')->default('0');
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
        Schema::dropIfExists('forecast_winter4_models');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForecastWinter2ModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forecast_winter2_models', function (Blueprint $table) {
            $table->increments('id_forecast_winter2_model');
            $table->integer('Forecastwinter2Product')->unsigned();
            $table->foreign('Forecastwinter2Product')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->double('alfa_winter2')->default(0.5);
            $table->double('beta_winter2')->default(0.5);
            $table->double('gamma_winter2')->default(0.2);
            $table->double('level_winter2')->default(0);
            $table->double('trend_winter2')->default(1);
            $table->double('factor1_winter2')->default(1);
            $table->double('factor2_winter2')->default(1);
            $table->integer('initial_month_winter2')->default(1);
            $table->double('1')->default('0');
            $table->double('2')->default('0');
            $table->double('3')->default('0');
            $table->double('4')->default('0');
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
        Schema::dropIfExists('forecast_winter2_models');
    }
}

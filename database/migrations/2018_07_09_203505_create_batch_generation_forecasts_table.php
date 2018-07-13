<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchGenerationForecastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_generation_forecasts', function (Blueprint $table) {
            $table->increments('id_generation_forecast');
            $table->integer('GenerationForecast')->unsigned();
            $table->foreign('GenerationForecast')->on('historical_datas')->references('id_historical_data')->onDelete('cascade')->onUpdate('cascade');
            $table->string('GenerationForecastModel',2)->nullable();
            $table->dateTime('booking_generation_forecast');
            $table->boolean('executed_generation_forecast')->default('0');
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
        Schema::dropIfExists('batch_generation_forecasts');
    }
}

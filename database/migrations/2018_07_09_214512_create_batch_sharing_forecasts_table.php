<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchSharingForecastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_sharing_forecasts', function (Blueprint $table) {
            $table->increments('id_sharing_forecast');
            $table->integer('sharing_forecast')->unsigned();
            $table->foreign('sharing_forecast')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->string('sharing_forecast_model',2)->nullable();
            $table->dateTime('booking_sharing_forecast');
            $table->boolean('executed_sharing_forecast')->default('0');
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
        Schema::dropIfExists('batch_sharing_forecasts');
    }
}

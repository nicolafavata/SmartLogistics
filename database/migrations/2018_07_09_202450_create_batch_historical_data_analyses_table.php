<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchHistoricalDataAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_historical_data_analyses', function (Blueprint $table) {
            $table->increments('id_batch_historical_data_analysi');
            $table->integer('HistoricalDataAnalysis')->unsigned();
            $table->foreign('HistoricalDataAnalysis')->on('historical_datas')->references('id_historical_data')->onDelete('cascade')->onUpdate('cascade');
            $table->dateTime('booking_historical_data_analysi');
            $table->boolean('executed')->default('0');
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
        Schema::dropIfExists('batch_historical_data_analyses');
    }
}

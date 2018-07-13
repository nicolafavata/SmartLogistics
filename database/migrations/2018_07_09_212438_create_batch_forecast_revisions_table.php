<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchForecastRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_forecast_revisions', function (Blueprint $table) {
            $table->increments('id_forecast_revision');
            $table->integer('forecast_revision')->unsigned();
            $table->foreign('forecast_revision')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->string('RevisionForecastModel',2)->nullable();
            $table->dateTime('booking_revision_forecast');
            $table->boolean('executed_revision_forecast')->default('0');
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
        Schema::dropIfExists('batch_forecast_revisions');
    }
}

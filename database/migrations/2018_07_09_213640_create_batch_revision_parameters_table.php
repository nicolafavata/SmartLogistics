<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchRevisionParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_revision_parameters', function (Blueprint $table) {
            $table->increments('id_revision_parameter');
            $table->integer('revision_parameter')->unsigned();
            $table->foreign('revision_parameter')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->string('revision_parameter_forecast_model',2)->nullable();
            $table->dateTime('booking_revision_parameter');
            $table->boolean('executed_revision_parameter')->default('0');
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
        Schema::dropIfExists('batch_revision_parameters');
    }
}

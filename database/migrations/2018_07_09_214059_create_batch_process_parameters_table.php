<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchProcessParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_process_parameters', function (Blueprint $table) {
            $table->increments('id_process_parameter');
            $table->integer('process_parameter')->unsigned();
            $table->foreign('process_parameter')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->string('process_parameter_forecast_model',2)->nullable();
            $table->dateTime('booking_process_parameter');
            $table->boolean('executed_process_parameter')->default('0');
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
        Schema::dropIfExists('batch_process_parameters');
    }
}

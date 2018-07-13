<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchExpiriesMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_expiries_monitorings', function (Blueprint $table) {
            $table->increments('id_batchExpMon');
            $table->integer('days_batchExpMon');
            $table->integer('employee_batchExpMon')->unsigned();
            $table->foreign('employee_batchExpMon')->on('employees')->references('id_employee')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('warned')->default('0');
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
        Schema::dropIfExists('batch_expiries_monitorings');
    }
}

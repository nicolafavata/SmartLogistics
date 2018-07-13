<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchMonitoringOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_monitoring_orders', function (Blueprint $table) {
            $table->increments('id_batch_monitoring_order');
            $table->integer('company_batchMonOrder')->unsigned();
            $table->foreign('company_batchMonOrder')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('configOrder_batchMonOrder')->unsigned();
            $table->foreign('configOrder_batchMonOrder')->on('config_orders')->references('id_config_order')->onDelete('cascade')->onUpdate('cascade');
            $table->dateTime('limit_day_batch_monitoring_order')->nullable();
            $table->integer('window_first_batch_monitoring_order')->default(0);
            $table->integer('windows_last_batch_monitoring_order')->default(0);
            $table->dateTime('date_batch_monitoring_order')->nullable();
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
        Schema::dropIfExists('batch_monitoring_orders');
    }
}

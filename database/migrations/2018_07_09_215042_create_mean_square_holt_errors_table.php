<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeanSquareHoltErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mean_square_holt_errors', function (Blueprint $table) {
            $table->increments('id_mean_square_holt');
            $table->integer('mean_square_holt')->unsigned();
            $table->foreign('mean_square_holt')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->double('alfa_mean_square_holt')->default(0.2);
            $table->double('beta_mean_square_holt')->default(0.2);
            $table->double('level_mean_square_holt')->default(0);
            $table->double('trend_mean_square_holt')->default(1);
            $table->integer('month_mean_square_holt')->default('1');
            $table->double('mean_square_holt_error')->default('0');
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
        Schema::dropIfExists('mean_square_holt_errors');
    }
}

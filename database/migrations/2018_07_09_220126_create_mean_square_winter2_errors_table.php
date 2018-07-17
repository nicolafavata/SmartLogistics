<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeanSquareWinter2ErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mean_square_winter2_errors', function (Blueprint $table) {
            $table->increments('id_mean_square_winter2');
            $table->integer('mean_square_winter2')->unsigned();
            $table->foreign('mean_square_winter2')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->double('alfa_mean_square_winter2')->default(0.2);
            $table->double('beta_mean_square_winter2')->default(0.2);
            $table->double('gamma_mean_square_winter2')->default(0.2);
            $table->double('level_mean_square_winter2')->nullable();
            $table->double('trend_mean_square_winter2')->nullable();
            $table->double('factor1_mean_square_winter2')->nullable();
            $table->double('factor2_mean_square_winter2')->nullable();
            $table->integer('month_mean_square_winter2')->nullable();
            $table->double('mean_square_winter2_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mean_square_winter2_errors');
    }
}

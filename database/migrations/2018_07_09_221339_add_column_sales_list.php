<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSalesList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_lists', function (Blueprint $table) {
            $table->double('1')->default('0');
            $table->double('2')->default('0');
            $table->double('3')->default('0');
            $table->double('4')->default('0');
            $table->double('5')->default('0');
            $table->double('6')->default('0');
            $table->double('7')->default('0');
            $table->double('8')->default('0');
            $table->double('9')->default('0');
            $table->double('10')->default('0');
            $table->double('11')->default('0');
            $table->double('12')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

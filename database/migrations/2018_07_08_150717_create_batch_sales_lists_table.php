<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchSalesListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_sales_lists', function (Blueprint $table) {
            $table->increments('id_batch_sales_list');
            $table->integer('company_batch_sales_list')->unsigned();
            $table->foreign('company_batch_sales_list')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url_file_batch_sales_list',128)->default('0');
            $table->string('email_batch_sales_list')->nullable();
            $table->boolean('executed_batch_sales_list')->default('0');
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
        Schema::dropIfExists('batch_sales_lists');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDesksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_desks', function (Blueprint $table) {
            $table->increments('id_sales_desk');
            $table->integer('company_sales_desk')->unsigned();
            $table->foreign('company_sales_desk')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_sales_desk')->unsigned()->nullable();
            $table->foreign('customer_sales_desk')->on('customer_registries')->references('id_customer_registry')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer-company_sales_desk')->unsigned()->nullable();
            $table->foreign('customer-company_sales_desk')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('number_sales_desk')->default(0);
            $table->dateTime('date_sales_desk')->nullable();
            $table->double('total_sales_desk')->default(0);
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
        Schema::dropIfExists('sales_desks');
    }
}

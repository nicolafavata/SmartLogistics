<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDdtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_ddts', function (Blueprint $table) {
            $table->increments('id_sales_ddts');
            $table->integer('company_sales_ddts')->unsigned();
            $table->foreign('company_sales_ddts')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_sales_ddts')->unsigned()->nullable();
            $table->foreign('customer_sales_ddts')->on('customer_registries')->references('id_customer_registry')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer-company_sales_ddts')->unsigned()->nullable();
            $table->foreign('customer-company_sales_ddts')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('number_sales_ddts')->default(0);
            $table->dateTime('date_sales_ddts')->nullable();
            $table->double('total_sales_ddts')->default(0);
            $table->boolean('state_sales_ddts')->default('0');
            //riferimento fattura
            $table->integer('reference_sales_ddts')->unsigned()->nullable();
            $table->foreign('reference_sales_ddts')->on('sales_invoices')->references('id_sales_invoice')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('sales_ddts');
    }
}

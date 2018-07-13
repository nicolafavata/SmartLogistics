<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->increments('id_sales_invoice');
            $table->integer('company_sales_invoice')->unsigned();
            $table->foreign('company_sales_invoice')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_sales_invoice')->unsigned()->nullable();
            $table->foreign('customer_sales_invoice')->on('customer_registries')->references('id_customer_registry')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer-company-sales_invoice')->unsigned()->nullable();
            $table->foreign('customer-company-sales_invoice')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('number_sales_invoice')->default(0);
            $table->dateTime('date_sales_invoice')->nullable();
            $table->double('total_sales_invoice')->default(0);
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
        Schema::dropIfExists('sales_invoices');
    }
}

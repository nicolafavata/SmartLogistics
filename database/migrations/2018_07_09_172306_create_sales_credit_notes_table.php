<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesCreditNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_credit_notes', function (Blueprint $table) {
            $table->increments('id_sales_credit_note');
            $table->integer('company_sales_credit_note')->unsigned();
            $table->foreign('company_sales_credit_note')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_sales_credit_note')->unsigned()->nullable();
            $table->foreign('customer_sales_credit_note')->on('customer_registries')->references('id_customer_registry')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer-company-sales_credit_note')->unsigned()->nullable();
            $table->foreign('customer-company-sales_credit_note')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('number_sales_credit_note')->default(0);
            $table->dateTime('date_sales_credit_note')->nullable();
            $table->double('total_sales_credit_note')->default(0);
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
        Schema::dropIfExists('sales_credit_notes');
    }
}

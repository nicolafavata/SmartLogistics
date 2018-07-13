<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesInvoiceContsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_invoice_conts', function (Blueprint $table) {
            $table->increments('id_salesInvCon');
            $table->integer('invoice_salesInvCon')->unsigned();
            $table->foreign('invoice_salesInvCon')->on('sales_invoices')->references('id_sales_invoice')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('product_salesInvCon')->unsigned();
            $table->foreign('product_salesInvCon')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity_salesInvCon')->nullable();
            $table->double('discount_salesInvCon')->default('0');
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
        Schema::dropIfExists('sales_invoice_conts');
    }
}

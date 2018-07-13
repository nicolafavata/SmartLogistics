<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_customer_orders', function (Blueprint $table) {
            $table->increments('id_salesCust-or');
            $table->integer('company_salesCust-or')->unsigned();
            $table->foreign('company_salesCust-or')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_salesCust-or')->unsigned()->nullable();
            $table->foreign('customer_salesCust-or')->on('customer_registries')->references('id_customer_registry')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('cust-com-salesCust-or')->unsigned()->nullable();
            $table->foreign('cust-com-salesCust-or')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('number_salesCust-or')->default(0);
            $table->dateTime('date_salesCust-or')->nullable();
            $table->double('total_salesCust-or')->default(0);
            $table->boolean('state_salesCust-or')->default('0');
            //riferimento fattura
            $table->integer('reference_salesCust-or')->unsigned()->nullable();
            $table->foreign('reference_salesCust-or')->on('sales_invoices')->references('id_sales_invoice')->onDelete('cascade')->onUpdate('cascade');
            //riferimento vendita al banco
            $table->integer('ref-desk_salesCust-or')->unsigned()->nullable();
            $table->foreign('ref-desk_salesCust-or')->on('sales_desks')->references('id_sales_desk')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('sales_customer_orders');
    }
}

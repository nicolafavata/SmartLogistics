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
            $table->increments('id_salesCust_or');
            $table->integer('company_salesCust_or')->unsigned();
            $table->foreign('company_salesCust_or')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('customer_salesCust_or')->unsigned()->nullable();
            $table->foreign('customer_salesCust_or')->on('customer_registries')->references('id_customer_registry')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('cust-com-salesCust_or')->unsigned()->nullable();
            $table->foreign('cust-com-salesCust_or')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('number_salesCust_or')->default(0);
            $table->dateTime('date_salesCust_or')->nullable();
            $table->double('total_salesCust_or')->default(0);
            $table->boolean('state_salesCust_or')->default('0');
            //riferimento fattura
            $table->integer('reference_salesCust_or')->unsigned()->nullable();
            $table->foreign('reference_salesCust_or')->on('sales_invoices')->references('id_sales_invoice')->onDelete('cascade')->onUpdate('cascade');
            //riferimento vendita al banco
            $table->integer('ref-desk_salesCust_or')->unsigned()->nullable();
            $table->foreign('ref-desk_salesCust_or')->on('sales_desks')->references('id_sales_desk')->onDelete('cascade')->onUpdate('cascade');
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

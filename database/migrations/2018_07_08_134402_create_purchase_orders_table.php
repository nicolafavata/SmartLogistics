<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id_purchase_order');
            $table->integer('company_purchase_order')->unsigned();
            $table->foreign('company_purchase_order')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('provider_purchase_order')->unsigned();
            $table->foreign('provider_purchase_order')->on('providers')->references('id_provider')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('order_number_purchase')->nullable();
            $table->dateTime('order_date_purchase')->nullable();
            $table->string('state_purchase_order',2)->nullable();
            $table->string('comment_purchase_order',190)->nullable();
            $table->double('total_purchase_order')->default(0);
            $table->string('reference_purchase_order',40)->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
}

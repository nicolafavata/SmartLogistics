<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesCreditNotesContsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_credit_notes_conts', function (Blueprint $table) {
            $table->increments('id_salesCrNoCo');
            $table->integer('creditNote')->unsigned();
            $table->foreign('creditNote')->on('sales_credit_notes')->references('id_sales_credit_note')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('product_salesCrNoCo')->unsigned();
            $table->foreign('product_salesCrNoCo')->on('sales_lists')->references('id_sales_list')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity_salesCrNoCo')->nullable();
            $table->double('discount_salesCrNoCo')->default('0');
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
        Schema::dropIfExists('sales_credit_notes_conts');
    }
}

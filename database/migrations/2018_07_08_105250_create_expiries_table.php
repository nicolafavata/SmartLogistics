<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expiries', function (Blueprint $table) {
            $table->increments('id_expiry');
            $table->integer('company_expiry')->unsigned();
            //Relazione sede con scadenza di un prodotto
            $table->foreign('company_expiry')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('inventory_expiry')->unsigned();
            //Relazione prodotto con scadenza
            $table->foreign('inventory_expiry')->on('inventories')->references('id_inventory')->onDelete('cascade')->onUpdate('cascade');
            $table->float('stock_expiry',10,2)->default(0);
            $table->dateTime('date_expiry')->nullable();
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
        Schema::dropIfExists('expiries');
    }
}

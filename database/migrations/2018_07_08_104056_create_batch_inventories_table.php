<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_inventories', function (Blueprint $table) {
            $table->increments('id_batch_inventory');
            $table->integer('company_batch_inventory')->unsigned();
            //Relazione sede con prenotazioni per elaborazione inventario
            $table->foreign('company_batch_inventory')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url_file_batch_inventory',128)->default('0');
            $table->string('email_batch_inventory')->nullable();
            $table->boolean('executed_batch_inventory')->default('0');
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
        Schema::dropIfExists('batch_inventories');
    }
}

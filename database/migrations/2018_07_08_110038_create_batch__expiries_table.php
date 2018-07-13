<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchExpiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_expiries', function (Blueprint $table) {
            $table->increments('id_batch_expiries');
            $table->integer('company_batch_expiries')->unsigned();
            //Relazione sede con prenotazioni per elaborazione inventario
            $table->foreign('company_batch_expiries')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url_file_batch_expiries',128)->default('0');
            $table->string('email_batch_expiries')->nullable();
            $table->boolean('executed_batch_expiries')->default('0');
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
        Schema::dropIfExists('batch__expiries');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_productions', function (Blueprint $table) {
            $table->increments('id_batch_production');
            $table->integer('company_batch_production')->unsigned();
            $table->foreign('company_batch_production')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url_file_batch_production',128)->default('0');
            $table->string('email_batch_production')->nullable();
            $table->boolean('executed_batch_production')->default('0');
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
        Schema::dropIfExists('batch_productions');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchHistoricalDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_historical_datas', function (Blueprint $table) {
            $table->increments('id_batchHisDat');
            $table->integer('company_batchHisDat')->unsigned();
            $table->foreign('company_batchHisDat')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url_batchHisDat',128)->default('0');
            $table->string('email_batchHisDat')->nullable();
            $table->boolean('executed_batchHisDat')->default('0');
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
        Schema::dropIfExists('batch_historical_datas');
    }
}

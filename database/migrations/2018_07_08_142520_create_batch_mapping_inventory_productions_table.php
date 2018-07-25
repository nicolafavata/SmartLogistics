<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchMappingInventoryProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_mapping_productions', function (Blueprint $table) {
            $table->increments('id_batch_mapping_production');
            $table->integer('company_batch_map_pro')->unsigned();
            $table->foreign('company_batch_map_pro')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url_file_batch_map_pro',128)->default('0');
            $table->string('email_batch_map_pro')->nullable();
            $table->boolean('executed_batch_map_pro')->default('0');
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
        Schema::dropIfExists('batch_mapping_inventory_productions');
    }
}

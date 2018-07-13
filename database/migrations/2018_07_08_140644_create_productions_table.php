<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->increments('id_production');
            $table->integer('company_production')->unsigned();
            $table->foreign('company_production')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('cod_production',20)->nullable();
            $table->string('title_production',80)->nullable();
            $table->string('category_first_production',50)->nullable();
            $table->string('category_second_production',50)->nullable();
            $table->string('unit_production',2)->nullable();
            $table->string('url_production',190)->nullable();
            $table->longText('description_production')->nullable();
            $table->string('brand_production',30)->nullable();
            $table->string('ean_production',18)->nullable();
            //Informazioni iva
            $table->integer('codice_iva_production')->nullable();
            $table->integer('imposta_production')->nullable();
            $table->string('imposta_desc_production',42)->nullable();
            //dimensioni e peso
            $table->float('height_production',8,2)->nullable();
            $table->float('width_production',8,2)->nullable();
            $table->float('depth_production',8,2)->nullable();
            $table->float('weight_production',8,2)->nullable();
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
        Schema::dropIfExists('productions');
    }
}

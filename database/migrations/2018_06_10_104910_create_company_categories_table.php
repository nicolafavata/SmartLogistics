<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_categories', function (Blueprint $table) {
            $table->increments('id_company_categoria');
            //Relazione molti a molti fra la tabella categories e company_offices
            $table->integer('company')->unsigned();
            $table->index('company')->on('company_offices')->references('id_company_office');
            //
            $table->integer('categoria')->unsigned();
            $table->index('categoria')->on('categories')->references('id_categoria');
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
        Schema::dropIfExists('company_categories');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisibleComunesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visible_comuni', function (Blueprint $table) {
            $table->increments('id_visible_comune');
            $table->integer('cap_visible')->nullable();
            //Una sede può essere visibile in più comuni
            $table->foreign('cap_visible')->on('comuni')->references('id_comune');
            $table->integer('company_office_visible')->unsigned()->nullable();
            //E' una relazione molti a molti tra una sede e un comune
            $table->foreign('company_office_visible')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('visible_comunes');
    }
}

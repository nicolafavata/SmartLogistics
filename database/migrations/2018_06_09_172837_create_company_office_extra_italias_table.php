<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyOfficeExtraItaliasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_offices_extra_italia', function (Blueprint $table) {
            $table->increments('id_company_office_extra');
            $table->string('cap_company_office_extra',8);
            $table->string('city_company_office_extra',30);
            $table->string('state_company_office_extra',30)->nullable();
            $table->integer('company_office')->unsigned();
            //E' una relazione uno a uno tra una sede e un indirizzo extra italia
            $table->foreign('company_office')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('company_office_extra_italias');
    }
}

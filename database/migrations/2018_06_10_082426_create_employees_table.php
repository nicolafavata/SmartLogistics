<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id_employee');
            $table->integer('user_employee')->unsigned();
            //Un impiegato corrisponde un utente, nella tabella user la relazione è 0 - 1
            $table->foreign('user_employee')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('matricola',16)->nullable();
            $table->string('tel_employee',16)->nullable();
            $table->string('cell_employee',16)->nullable();
            $table->string('img_employee',128)->nullable();
            $table->integer('company_employee')->unsigned();
            //Un impiegato corrisponde una sede aziendale, a una sede aziendale da 0 a + impiegati
            $table->foreign('company_employee')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('responsabile')->default('0');
            $table->boolean('acquisti')->default('0');
            $table->boolean('produzione')->default('0');
            $table->boolean('vendite')->default('0');
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
        Schema::dropIfExists('employees');
    }
}

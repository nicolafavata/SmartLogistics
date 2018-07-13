<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->increments('id_provider');
            //Un fornitore può essere una sede presente in piattaforma o in aggregazione supply chain
            $table->integer('company_provider')->unsigned()->nullable();
            //E' una relazione uno a uno tra due sedi
            $table->foreign('company_provider')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('supply_provider')->default('0');
            //-- fine ridondanza
            $table->string('provider_cod',10);
            $table->string('rag_soc_provider',50)->nullable();
            $table->string('address_provider',150)->nullable();
            $table->string('telefono_provider',16)->nullable();
            $table->string('email_provider',100)->nullable();
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
        Schema::dropIfExists('providers');
    }
}

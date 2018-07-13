<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerRegistriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_registries', function (Blueprint $table) {
            $table->increments('id_customer_registry');
            $table->integer('company_customer_registry')->unsigned();
            $table->foreign('company_customer_registry')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('user_customer_registry')->unsigned()->nullable();
            $table->foreign('user_customer_registry')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('office_customer_registry')->unsigned()->nullable();
            $table->foreign('office_customer_registry')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('rag_soc_customer',50);
            $table->string('address_customer',60);
            $table->integer('cap_customer');
            $table->foreign('cap_customer')->on('comuni')->references('id_comune');
            $table->string('telefono_customer',16)->nullable();
            $table->string('email_customer',30)->nullable();
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
        Schema::dropIfExists('customer_registries');
    }
}

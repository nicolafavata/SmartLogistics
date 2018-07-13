<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_orders', function (Blueprint $table) {
            $table->increments('id_config_order');
            $table->integer('company_config_order')->unsigned();
            //Relazione sede con configurazione monitoraggio ordini
            $table->foreign('company_config_order')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            //Relazione con il fornitore
            $table->integer('provider_config_order')->unsigned();
            //Relazione fornitore
            $table->foreign('provider_config_order')->on('providers')->references('id_provider')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('lead_time_config')->default(0);
            $table->integer('window_first_config')->default(0);
            $table->integer('windows_last_config')->default(0);
            $table->double('min_import_config')->default(0);
            $table->double('max_import_config')->default(0);
            $table->binary('mapping_config')->nullable();
            $table->boolean('transmission_config')->default('0');
            $table->boolean('execute_config')->default('0');
            $table->integer('days_number_config')->default(0);
            $table->boolean('level_config')->default('0');
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
        Schema::dropIfExists('config_orders');
    }
}

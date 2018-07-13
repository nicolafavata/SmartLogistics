<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id_inventory');
            $table->integer('company_inventory')->unsigned();
            //Una sede può avere più prodotti nell'inventario
            $table->foreign('company_inventory')->on('company_offices')->references('id_company_office')->onDelete('cascade')->onUpdate('cascade');
            $table->string('cod_inventory',50)->nullable();
            $table->string('title_inventory',80)->nullable();
            $table->string('category_first',50)->nullable();
            $table->string('category_second',50)->nullable();
            $table->string('unit_inventory',2)->nullable();
            $table->float('stock',10,2)->default(0);
            $table->float('committed',10,2)->default(0);
            $table->float('arriving',10,2)->default(0);
            $table->string('url_inventory',190)->default('0');
            $table->longText('description_inventory')->nullable();
            $table->string('brand',30)->nullable();
            $table->string('ean_inventory',18)->nullable();
            $table->double('average_cost_inventory')->default(0);
            $table->double('last_cost_inventory')->default(0);
            //Informazioni iva
            $table->integer('codice_iva_inventory')->nullable();
            $table->integer('imposta_inventory')->nullable();
            $table->string('imposta_desc_inventory',42)->nullable();
            //dimensioni
            $table->float('height_inventory',8,2)->nullable();
            $table->float('width_inventory',8,2)->nullable();
            $table->float('depth_inventory',8,2)->nullable();
            $table->float('weight_inventory',8,2)->nullable();
            //logica
            $table->boolean('expire_inventory')->default('0');
            $table->boolean('sale_inventory')->default('0');
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
        Schema::dropIfExists('inventories');
    }
}

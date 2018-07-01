<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('business')->default('0');
            $table->boolean('admin')->default('0');
            $table->boolean('profile')->default('0');
            $table->string('name');
            $table->string('cognome');
            $table->boolean('gdpr')->nullable();
            $table->string('email')->unique();
            $table->string('capnow',5)->nullable();
            $table->string('comunenow',45)->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements("id");
            $table->string("username");
            $table->string("lastName")->nullable();
            $table->string("firstName")->nullable();
            $table->string("company")->nullable();
            $table->string("email")->nullable();
            $table->string("password");
            $table->string("phoneNumber")->nullable();
            $table->bigInteger('fonction_id')->unsigned();
            $table->foreign('fonction_id')->references('id')->on('fonctions')->onDelete('cascade');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDamageTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damage_types', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name");
            $table->boolean("important")->default(0);
            $table->bigInteger('profile_group_id')->unsigned();
            $table->foreign('profile_group_id')->references('id')->on('profile_groups')->onDelete('cascade');
            $table->bigInteger('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
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
        Schema::dropIfExists('damage_types');
    }
}

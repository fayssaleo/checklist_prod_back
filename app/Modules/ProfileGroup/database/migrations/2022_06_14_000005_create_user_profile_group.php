<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfileGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profile_group', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();

            $table->bigInteger('profile_group_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')

                ->onDelete('cascade');

            $table->foreign('profile_group_id')->references('id')->on('profile_groups')

                ->onDelete('cascade');

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
        Schema::dropIfExists('user_profile_group');
    }
}

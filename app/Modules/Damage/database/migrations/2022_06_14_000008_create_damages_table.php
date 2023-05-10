<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDamagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damages', function (Blueprint $table) {

            $table->bigIncrements("id");

            $table->string("status")->default("on progress");
            $table->text("description")->nullable();
            $table->text("resolveDescription")->nullable();
            $table->string("shift")->nullable();
      

            $table->bigInteger('driverOut')->unsigned()->nullable();
            $table->foreign('driverOut')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('driverIn')->unsigned()->nullable();
            $table->foreign('driverIn')->references('id')->on('users')->onDelete('cascade');



            $table->bigInteger('declaredBy_id')->unsigned()->nullable();
            $table->dateTime("declaredAt")->nullable();
            $table->foreign('declaredBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('confirmedBy_id')->unsigned()->nullable();
            $table->dateTime("confirmedAt")->nullable();
            $table->foreign('confirmedBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('closedBy_id')->unsigned()->nullable();
            $table->dateTime("closedAt")->nullable();
            $table->foreign('closedBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('rejectedBy_id')->unsigned()->nullable();
            $table->dateTime("rejectedAt")->nullable();
            $table->text("rejectedDescription")->nullable();
            $table->integer("rejectedTimes")->default(0);
            $table->foreign('rejectedBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('equipment_id')->unsigned()->nullable();
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');

            $table->bigInteger('damage_type_id')->unsigned()->nullable();
            $table->foreign('damage_type_id')->references('id')->on('damage_types')->onDelete('cascade');

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
        Schema::dropIfExists('damages');
    }
}

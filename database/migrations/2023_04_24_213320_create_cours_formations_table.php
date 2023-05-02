<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cours_formations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cour_id')->unsigned();
            $table->integer('formation_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('cours_formations', function ($table) {
            $table->foreign('cour_id')->references('id')->on('cours')->onDelete('cascade');
            $table->foreign('formation_id')->references('id')->on('formations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cours_formations');
    }
};

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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->string('prenom');
            $table->string('identifiant')->unique();
            $table->string('email')->unique();
            $table->string('nationalite');
            $table->date('date_naissance');
            $table->string('telephone');
            $table->string('photo')->nullable();
            $table->string('password');
            $table->enum('role', array('invité','banni','étudiant','professeur','admin'));
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
};

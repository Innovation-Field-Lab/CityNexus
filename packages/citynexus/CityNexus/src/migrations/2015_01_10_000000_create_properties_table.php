<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_address')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street')->nullable();
            $table->string('unit')->nullable();
            $table->string('city')->default('chealsa');
            $table->string('state')->default('ma');
            $table->string('zip')->default('02150');
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->integer('map')->nullable();
            $table->string('lot')->nullable();
            $table->string('type')->nullable();
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
        Schema::drop('properties');
    }
}

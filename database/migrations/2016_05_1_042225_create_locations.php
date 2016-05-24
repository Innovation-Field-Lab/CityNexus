<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->float('lat')->nullable();
            $table->float('long')->nullable();
            $table->string('description')->nullable();
            $table->string('full_address')->nullable();
            $table->string('source')->nullable();
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
        Schema::drop('citynexus_locations');
    }
}

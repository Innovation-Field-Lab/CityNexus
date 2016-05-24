<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_properties', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('full_address')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('street_type')->nullable();
            $table->string('unit')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->integer('map')->nullable();
            $table->string('lot')->nullable();
            $table->string('type')->nullable();
            $table->string('tiger_line_id')->nullable();
            $table->string('side')->nullable();
            $table->integer('alias_of')->nullable();
            $table->boolean('review')->default(false);
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
        Schema::drop('citynexus_properties');
    }

}

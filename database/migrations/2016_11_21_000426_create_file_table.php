<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     * d
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_files', function (Blueprint $table) {
            $table->increments('id')->unsigned();;
            $table->integer('location_id')->nullable();
            $table->integer('property_id')->nullable();
            $table->string('caption')->nullable();
            $table->string('description')->nullable();
            $table->integer('version_id');
            $table->softDeletes();
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
        Schema::drop('citynexus_file');
    }
}

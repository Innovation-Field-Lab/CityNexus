<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
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
            $table->string('size')->nullable();
            $table->string('type');
            $table->integer('added_by')->unsigned();
            $table->integer('version_id')->unsigned();
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

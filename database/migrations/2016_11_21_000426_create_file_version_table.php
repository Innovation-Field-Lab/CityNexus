<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_file_versions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('added_by')->unsigned();
            $table->integer('file_id')->unsigned();
            $table->integer('size')->unsigned;
            $table->string('type');
            $table->string('source');
            $table->dateTime('added_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('citynexus_file_versions');
    }
}

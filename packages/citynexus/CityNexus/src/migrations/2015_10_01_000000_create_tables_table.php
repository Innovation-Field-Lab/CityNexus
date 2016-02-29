<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabler_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name')->nullable();
            $table->string('table_title')->nullable();
            $table->string('name')->nullable();
            $table->string('table_description')->nullable();
            $table->json('scheme')->nullable();
            $table->json('raw_upload')->nullable();
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
        Schema::drop('tabler_tables');
    }
}

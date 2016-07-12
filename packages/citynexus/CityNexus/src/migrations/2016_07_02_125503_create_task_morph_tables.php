<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskMorphTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_taskables', function (Blueprint $table) {
            $table->integer('task_id');
            $table->integer('citynexus_taskable_id');
            $table->string('citynexus_taskable_type');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('citynexus_taskables');
    }
}

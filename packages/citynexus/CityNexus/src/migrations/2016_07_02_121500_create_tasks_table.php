<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('task')->nullable();
            $table->text('description')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->dateTime('due_at')->nullable();;
            $table->dateTime('completed_at')->nullable();
            $table->integer('completed_by')->nullable();
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
        Schema::drop('citynexus_tasks');
    }
}

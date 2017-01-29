<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApirequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_api_requests', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('request');
            $table->integer('user_id')->unsigned();
            $table->json('settings');
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
        Schema::drop('citynexus_api_requests');
    }
}

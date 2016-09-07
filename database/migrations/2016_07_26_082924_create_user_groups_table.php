<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citynexus_user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('permissions_json');
            $table->timestamps();
        });

        Schema::create('groups_users', function (Blueprint $table) {
            $table->integer('user_group_id');
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('citynexus_user_groups');
        Schema::drop('groups_users');
    }
}

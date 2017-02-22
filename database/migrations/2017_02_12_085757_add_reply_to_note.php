<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReplyToNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('citynexus_notes', function (Blueprint $table) {
            $table->integer('reply_to')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('citynexus_notes', function (Blueprint $table) {
            $table->dropColumn('reply_to');
        });
    }
}

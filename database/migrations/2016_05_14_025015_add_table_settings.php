<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('tabler_tables', 'settings'))
        {
            Schema::table('tabler_tables', function (Blueprint $table) {
                $table->json('settings')->nullable();
                $table->dropColumn('timestamp');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabler_tables', function (Blueprint $table) {
            //
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_tag', function (Blueprint $table) {
            $table->dateTime('created_at')->default('2000-01-01 01:00:00');
            $table->dateTime('updated_at')->default('2000-01-01 01:00:00');
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_tag', function(Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropTimestamps();
            $table->dropColumn(['created_by', 'deleted_by']);
        });
    }
}

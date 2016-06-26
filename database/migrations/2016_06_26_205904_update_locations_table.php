<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('citynexus_locations', function (Blueprint $table) {
            $table->string('address')->nullable()->unique();
            $table->json('polygon')->nullable();
            $table->string('street_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('locality')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('sub_locality')->nullable();
            $table->string('admin_levels')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('timezone')->nullable();
            $table->json('raw')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('citynexus_locations', function (Blueprint $table) {
            $table->dropColumn(['address', 'polygon', 'street_number',
                'street_name', 'locality', 'postal_code', 'sub_locality',
                'admin_levels', 'country', 'country_code', 'timezone', 'raw']);
        });
    }
}

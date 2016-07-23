<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultWidgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tags = [
            'name' => "Recently Tagged",
            'description' => "Listing of 20 most recently tagged properties",
            'type' => 'tags',
            'settings' => json_encode(['system_default' => true, 'pull_length' => 20])
        ];
        $comments = [
            'name' => "Recent Comments",
            'description' => "Listing of 20 most recently notes",
            'type' => 'comments',
            'settings' => json_encode(['system_default' => true, 'pull_length' => 20])
        ];

        $comment = \CityNexus\CityNexus\Widget::create($comments);
        $tag = \CityNexus\CityNexus\Widget::create($tags);

        \CityNexus\CityNexus\Setting::create(['key' => 'globalDashboard', 'value' => \GuzzleHttp\json_encode([$comment->id, $tag->id])]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

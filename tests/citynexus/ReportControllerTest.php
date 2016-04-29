<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CityNexus\CityNexus\TableBuilder;

class ReportControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetScatterChart()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"create": "true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\ReportsController@getScatterChart'))->assertResponseOk();
    }
}

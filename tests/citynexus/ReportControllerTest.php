<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CityNexus\CityNexus\TableBuilder;
use CityNexus\CityNexus\Http\ReportsController;

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

    public function testDistributionCurve()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"create": "true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\ReportsController@getDistributionCurve'))->assertResponseOk();
    }

    public function testHeatMap()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"create": "true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\ReportsController@getHeatMap'))->assertResponseOk();
    }

}

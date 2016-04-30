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

    public function testGetDataSetScore()
    {
        $reports = new ReportsController();
        $score = \CityNexus\CityNexus\Score::first();
        $expected = DB::table('citynexus_scores_' . $score->id)->lists('property_id', 'score');
        $result = $reports->getDataSet( ['score' => $score->id]);
        $this->assertSame($expected, $result, 'Get property data list for a score');

        $dataset = \CityNexus\CityNexus\Table::first();
        $expected = DB::table($dataset->table_name)->orderBy('created_at')->lists('property_id', 'created_at');
        $result = $reports->getDataSet( ['table_name' => $dataset->table_name, 'datafield' => 'created_at']);
        $this->assertSame($expected, $result, 'Get property data list for an existing dataset');
    }

}

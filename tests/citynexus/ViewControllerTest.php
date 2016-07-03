 <?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CityNexus\CityNexus\TableBuilder;
use CityNexus\CityNexus\Http\ViewController;

class ViewControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetScatterChart()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"view": "true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\ViewController@getScatterChart'))->assertResponseOk();
    }

    public function testDistribution()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"view": "true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\ViewController@getDistribution'))->assertResponseOk();
    }

    public function testHeatMap()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"view": "true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\ViewController@getHeatMap'))->assertResponseOk();
    }

    public function testPostSaveReport()
    {
       $this->withoutMiddleware();

        $settings = ['type' => 'heatmap', 'table' => 'citynexus_scores_1', 'key' => 'scores', 'intensity' => 50];
        $name = "Test Heatmap";
        $this->post(action('\CityNexus\CityNexus\Http\ViewController@postSaveView'), ['settings' => $settings, 'name' => $name])
            ->assertResponseStatus(200);
    }

    public function testPostSaveReportMissingData()
    {
        $this->withoutMiddleware();

        $settings = ['type' => 'heatmap', 'table' => 'citynexus_scores_1', 'key' => 'scores', 'intensity' => 50];
        $name = "Test Heatmap";
        $this->post(action('\CityNexus\CityNexus\Http\ViewController@postSaveView'), ['settings' => $settings])
            ->assertResponseStatus(500);
    }

    public function testViewIndex()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"view": "true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\ViewController@getIndex'))->see('View');

    }

    public function testRiskScoreHeatMapLinks()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"view": "true"}, "scores":{"view": "true", "create": "true"}}',

            ]
        );

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->click('Heat Map')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->click('Pin Map')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->click('Distribution Chart')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->click('Rankings')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->click('Remove Score')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->click('Duplicate Score')
            ->assertResponseOk();

    }
}

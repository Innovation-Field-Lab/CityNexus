<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CitynexusControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testNavigationWithoutPermissions()
    {
        $access = factory(App\User::class)->create();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->dontSee('All Properties')
            ->dontSee(action('\CityNexus\CityNexus\Http\TablerController@getIndex'))
            ->dontSee(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->dontSee(action('\CityNexus\CityNexus\Http\RiskScoreController@getCreate'))
            ->dontSee(action('\CityNexus\CityNexus\Http\TablerController@getUploader'));
    }

    public function testNavigationWithPermissions()
    {
        $access = factory(App\User::class)->create([
            'permissions' => '{"datasets":{"view":"true","raw":"true","create":"true","upload":"true","edit":"true","delete":"true","export":"true","rollback":"true"},"scores":{"view":"true","raw":"true","create":"true","refresh":"true","edit":"true","score":"true"},"usersAdmin":{"create":"true","delete":"true","assign":"true"},"properties":{"view":"true","show":"true","merge":"true","edit":"true"},"admin":{"view":"true"}}'
        ]);

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->see(action('\CityNexus\CityNexus\Http\PropertyController@getIndex'))
            ->see(action('\CityNexus\CityNexus\Http\TablerController@getIndex'))
            ->see(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))
            ->see(action('\CityNexus\CityNexus\Http\RiskScoreController@getCreate'))
            ->see(action('\CityNexus\CityNexus\Http\TablerController@getUploader'));
    }

    public function testNavigationLinks()
    {
        $access = factory(App\User::class)->create([
            'permissions' => '{"datasets":{"view":"true","raw":"true","create":"true","upload":"true","edit":"true","delete":"true","export":"true","rollback":"true"},"scores":{"view":"true","raw":"true","create":"true","refresh":"true","edit":"true","score":"true"},"usersAdmin":{"create":"true","delete":"true","assign":"true"},"properties":{"view":"true","show":"true","merge":"true","edit":"true"},"admin":{"view":"true"}, "reports":{"view":"true","create":"true"}}'
        ]);

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('All Properties')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('All Tags')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('All Data Sets')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('New From Upload')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('All Scores')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('Create New Score')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('Saved Views')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('Scatter Chart Builder')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('Distribution Curve Builder')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->click('Heat Map Builder')
            ->assertResponseOk();

    }
}

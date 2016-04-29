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
            ->dontSee('<li><a href="/citynexus/properties">All Properties</a></li>')
            ->dontSee('<li><a href="/tabler">All Data Sets</a></li>')
            ->dontSee('<li><a href="/citynexus/risk-score/scores">All Scores</a></li>')
            ->dontSee('<li><a href="/citynexus/risk-score/create">Create New Score</a></li>')
            ->dontSee('<li><a href="/tabler/uploader">New From Upload</a></li>');
    }

    public function testNavigationWithPermissions()
    {
        $access = factory(App\User::class)->create([
            'permissions' => '{"datasets":{"view":"true","raw":"true","create":"true","upload":"true","edit":"true","delete":"true","export":"true","rollback":"true"},"scores":{"view":"true","raw":"true","create":"true","refresh":"true","edit":"true","score":"true"},"usersAdmin":{"create":"true","delete":"true","assign":"true"},"properties":{"view":"true","show":"true","merge":"true","edit":"true"},"admin":{"view":"true"}}'
        ]);

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusController@getIndex'))
            ->see('<li><a href="/citynexus/properties">All Properties</a></li>')
            ->see('<li><a href="/tabler">All Data Sets</a></li>')
            ->see('<li><a href="/citynexus/risk-score/scores">All Scores</a></li>')
            ->see('<li><a href="/citynexus/risk-score/create">Create New Score</a></li>')
            ->see('<li><a href="/tabler/uploader">New From Upload</a></li>');
    }

    public function testGetProperty()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"properties":{"view":"true","show":"true","merge":"true","edit":"true"}}'
            ]
        );
        $this->actingAs($access)->visit('/' . config('citynexus.root_directory') . '/property/' . $access->id)->assertResponseOk();

    }

    public function testGetProperties()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"properties":{"view":"true","show":"true","merge":"true","edit":"true"}}'
            ]
        );
        $this->actingAs($access)->visit('/' . config('citynexus.root_directory') . '/properties')
            ->see('Merge Property')
            ->see('Details');

    }

    public function testGetPropertiesWithLimitedPermissions()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"properties":{"view":"true"}}'
            ]
        );
        $this->actingAs($access)->visit('/' . config('citynexus.root_directory') . '/properties')
            ->dontSee('Merge Property')
            ->dontSee('Details');

    }


}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetProperties()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"properties":{"view":"true","show":"true","merge":"true","edit":"true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\PropertyController@getIndex'))
            ->see('Merge Property')
            ->see('Details');

    }

    public function testShowProperty()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"properties":{"view":"true","show":"true","merge":"true","edit":"true"}}'
            ]
        );

        $property = factory(\CityNexus\CityNexus\Property::class)->create();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $property->id]))->see($property->full_address);

    }

    public function testGetPropertiesWithLimitedPermissions()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"properties":{"view":"true"}}'
            ]
        );

        $property = factory(\CityNexus\CityNexus\Property::class)->create();

//        $this->actingAs($access)->post(action('CityNexus\CityNexus\Http\City'))
//            ->dontSee('Merge Property')
//            ->dontSee('Details');

    }

    public function testPostAssociateTag()
    {

    }


}

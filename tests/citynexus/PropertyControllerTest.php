<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetProperties()
    {
        $property = factory(\CityNexus\CityNexus\Property::class)->create();
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


//    public function testCreateProperty()
//    {
//
//        $access = factory(App\User::class)->create(
//            [
//                'permissions' => '{"properties":{"create":"true"}}'
//            ]
//        );
//
//        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\PropertyController@getCreate'))
//            ->type('123', 'house_number')
//            ->type('Main', 'street_name')
//            ->select('street', 'street_type')
//            ->type('#1', 'unit')
//            ->press('Create Property');
//    }


}

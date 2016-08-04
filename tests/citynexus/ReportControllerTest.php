<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetCreateProperty()
    {
        $user = factory(App\User::class)->create(
            [
                'permissions' => '{"reports":{"create":"true"}}'
            ]);

        $this->actingAs($user)->visit(action('\CityNexus\CityNexus\Http\ReportController@getCreateProperty'))->assertResponseOk();
    }

//    public function testGetCreatePropertyFail()
//    {
//        $this->visit(action('\CityNexus\CityNexus\Http\ReportController@getCreateProperty'))->assertResponseStatus(500);
//    }
}

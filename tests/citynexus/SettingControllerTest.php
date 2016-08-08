<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testDoubleInvitingEmail()
    {

        $access = factory(App\User::class)->create(
            [
                'super_admin' => true
            ]
        );

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getCreateUser'))
            ->type('Test', 'first_name')
            ->type('McTesterson', 'last_name')
            ->type('test.mctesterson@gmail.com', 'email')
            ->type('Test Title', 'title')
            ->press('Invite User')
            ->assertResponseOk();

        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getIndex'))
            ->see('Test McTesterson');

        $this->visit(action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getCreateUser'))
            ->type('Test', 'first_name')
            ->type('McTesterson', 'last_name')
            ->type('test.mctesterson@gmail.com', 'email')
            ->press('Invite User')
            ->see('Oh oh! Looks like that email address already has an associated account.');
    }
}

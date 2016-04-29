<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetIndex()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"admin":{"view":"true", "delete":"true"}}'
            ]
        );
        $this->actingAs($access)->visit('/' . config('citynexus.root_directory') . '/admin/')->assertResponseOk();
    }

}

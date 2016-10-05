<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RiskscoreControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     *
     * Test Tabler Index page
     *
     */
    public function testGetCreateRiskscore()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"datasets":{"view":"true","raw":"true","create":"true","upload":"true","edit":"true","delete":"true","export":"true","rollback":"true"},"scores":{"view":"true","raw":"true","create":"true","refresh":"true","edit":"true","score":"true"},"usersAdmin":{"create":"true","delete":"true","assign":"true"},"properties":{"view":"true","show":"true","merge":"true","edit":"true"},"admin":{"view":"true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getCreate'))->assertResponseOk();
    }

    public function testGetScores()
    {
        $access = factory(App\User::class)->create(
            [
                'permissions' => '{"datasets":{"view":"true","raw":"true","create":"true","upload":"true","edit":"true","delete":"true","export":"true","rollback":"true"},"scores":{"view":"true","raw":"true","create":"true","refresh":"true","edit":"true","score":"true"},"usersAdmin":{"create":"true","delete":"true","assign":"true"},"properties":{"view":"true","show":"true","merge":"true","edit":"true"},"admin":{"view":"true"}}'
            ]
        );
        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'))->assertResponseOk();
    }
}

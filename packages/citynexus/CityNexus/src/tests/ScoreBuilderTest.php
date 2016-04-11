<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ScoreBuilderTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;

    /**
     *
     * Test Tabler Index page
     *
     */
    public function testRangeEquals ()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'range', 'test' => '5 ', 'range' => '=', 'result' => '2']));
        $value = 5;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(2, $result);
    }

    public function testAliasProperties()
    {
        $property = \CityNexus\CityNexus\Property::create();
        \CityNexus\CityNexus\Property::create(['alias_of' => $property->id]);


        dd($property);
    }

}

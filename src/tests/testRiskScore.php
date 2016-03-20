php<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CityNexus\CityNexus\ScoreBuilder;

class RiskScoreTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testFunctionWithRange()
    {

        $this->assertEquals($expected, $actual);
    }

}

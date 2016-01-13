<?php

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
        $score = [
            'table_id' => '1',
            'key' => 'field_1',
            'type' => 'range',
            'func' => '>',
            'test' => 5,
            'result' => 1
        ];
        $value = 10;

        $scorebuilder = new ScoreBuilder;

        $actual = $scorebuilder->calcScore($value, $score);

        $expected = 1;

        $this->assertEquals($expected, $actual);
    }

}

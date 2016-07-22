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
     * test runRange Function
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

    public function testRangeEqualsFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'range', 'test' => '5 ', 'range' => '=', 'result' => '2']));
        $value = 4;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertNotEquals(2, $result);
    }

    public function testRangeMoreThan()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'range', 'test' => '5 ', 'range' => '>', 'result' => '2']));
        $value = 7;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(2, $result);
    }

    public function testRangeMoreThanFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'range', 'test' => '5 ', 'range' => '>', 'result' => '2']));
        $value = 3;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertNotEquals(2, $result);
    }

    public function testRangeLessThan()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'range', 'test' => '5 ', 'range' => '<', 'result' => '2']));
        $value = 3;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(2, $result);
    }

    public function testRangeLessThanFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'range', 'test' => '5 ', 'range' => '<', 'result' => '2']));
        $value = 7;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertNotEquals(2, $result);
    }

    /**
     *
     * test runFunc Function
     *
     */
    public function testFunctionPlus()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'func', 'factor' => '5 ', 'func' => '+']));
        $value = 5;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(10, $result);
    }

    public function testFunctionPlusFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'func', 'factor' => '5 ', 'func' => '+']));
        $value = 4;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertNotEquals(10, $result);
    }

    public function testFunctionSubtract()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'func', 'factor' => '5 ', 'func' => '-']));
        $value = 10;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(5, $result);
    }

    public function testFunctionSubtractFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'func', 'factor' => '5 ', 'func' => '-']));
        $value = 8;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertNotEquals(5, $result);
    }

    public function testFunctionMultipy()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'func', 'factor' => '5 ', 'func' => '*']));
        $value = 5;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(25, $result);
    }

    public function testFunctionMultipyFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(['function' => 'func', 'factor' => '5 ', 'func' => '*']));
        $value = 4;
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertNotEquals(25, $result);
    }

    /**
     *
     * test runText Function
     *
     */
    public function testTextNotEmpty()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'notempty', 'test' => 'pass', 'result' => '5']));
        $value = 'fail';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(5, $result);
    }

    public function testTextEmpty()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'empty', 'result' => '5']));
        $value = '';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(5, $result);
    }

    public function testTextEquals()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'equals', 'test' => 'pass', 'result' => '5']));
        $value = 'pass';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(5, $result);
    }

    public function testTextEqualsFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'equals', 'test' => 'pass', 'result' => '5']));
        $value = 'fail';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertNotEquals(5, $result);
    }

    public function testTextDoesntEqual()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'doesntequal', 'test' => 'pass', 'result' => '5']));
        $value = 'fail';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(5, $result);
    }

    public function testTextDoesntEqualFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'doesntequal', 'test' => 'pass', 'result' => '5']));
        $value = 'pass';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(0, $result);
    }

    public function testTextContains()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'contains', 'test' => 'Pass', 'result' => '5']));
        $value = 'thisisapassingentry';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(5, $result);
    }
    public function testTextContainsFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'contains', 'test' => 'pass', 'result' => '5']));
        $value = 'thisisagoodentry';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);
        $this->assertEquals(0, $result);
    }
    public function testTextDoesntContain()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'doesntcontain', 'test' => 'pass', 'result' => '5']));
        $value = 'thisisagoodentry';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);


        $this->assertEquals(5, $result);
    }
    public function testTextDoesntContainFail()
    {
        $score = \GuzzleHttp\json_decode(json_encode(
            ['function' => 'doesntcontain', 'test' => 'pass', 'result' => '5']));
        $value = 'thisisapassingentry';
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();
        $result = $scorebuilder->calcElement($value, $score);


        $this->assertEquals(0, $result);
    }

    /**
     *
     * test genScore Function
     *
     */
    public function testGenScoreWithAlias()
    {
        $scorebuilder = new \CityNexus\CityNexus\ScoreBuilder();

        $property1 = \CityNexus\CityNexus\Property::create([
            'full_address' => '123 testing street',
            'house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street'
        ]);

        $property2 = \CityNexus\CityNexus\Property::create([
            'full_address' => '123a testing street',
            'house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street',
            'alias_of' => $property1->id
        ]);
        \Illuminate\Support\Facades\Schema::create('test_tabler', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id');
            $table->string('test');
        });

        \Illuminate\Support\Facades\DB::table('test_tabler')->insert(['property_id' => $property2->id, 'test' => 'success']);

        $score = \GuzzleHttp\json_decode(json_encode([
            [
                    'scope' => 'last',
                    'table_name' => 'test_tabler',
                    'key' => 'test',
                    'function' => 'equals',
                    'test' => 'success',
                    'result' => 5,
                    'period' => null
            ]
        ]));

        $result = $scorebuilder->genScore($property1, $score);

        \Illuminate\Support\Facades\Schema::drop('test_tabler');

        $this->assertEquals(5, $result);

    }
}

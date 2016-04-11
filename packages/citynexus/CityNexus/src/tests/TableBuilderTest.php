<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CityNexus\CityNexus\TableBuilder;

class TablerControllerTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;

    /**
     *
     * Test Tabler Index page
     *
     */
    public function testFindSyncID()
    {
        $tableBulider = new TableBuilder();

        $syncValues = [
            'house_number' => 'house_number',
            'street_name' => 'street_name',
            'street_type' => 'street_type'];

        $i = ['house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street'];

        $property = \CityNexus\CityNexus\Property::create([
            'full_address' => '123 testing street',
            'house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street'
        ]);

        $result = $tableBulider->findSyncId('citynexus_properties', $i, $syncValues);

        $this->assertSame($property->id, $result);
    }


    public function testUnitsInHouseNumbers()
    {
        $syncValues = [
            'house_number' => 'house_number',
            'street_name' => 'street_name',
            'street_type' => 'street_type'];
        $i = ['house_number' => '123 #5',
            'street_name' => 'testing',
            'street_type' => 'street'];

        $property = \CityNexus\CityNexus\Property::create([
            'full_address' => '123 testing street #5',
            'house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street',
            'unit' => '#5'
        ]);

        $tableBulider = new TableBuilder();

        $result = $tableBulider->findSyncId('citynexus_properties', $i, $syncValues);

        $this->assertSame($property->id, $result);
    }
}

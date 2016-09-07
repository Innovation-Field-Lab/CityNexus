 <?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CityNexus\CityNexus\TableBuilder;
use CityNexus\CityNexus\PropertySync;


class PropertySyncTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testCleanAddress()
    {
        $pre['full_address'] = '23 Monmouth St.';
        $expected['full_address'] = '23 monmouth st';

        $PSync = new PropertySync();

        $post = $this->invokeMethod($PSync, 'cleanAddress', array($pre));

        $this->assertSame($expected, $post);
    }

    public function testCleanAddressArray()
    {
        $pre = ['house_number' => '23', 'street_name' => 'Monmouth Street'];
        $expected = ['house_number' => '23', 'street_name' => 'monmouth street'];

        $PSync = new PropertySync();

        $post = $this->invokeMethod($PSync, 'cleanAddress', array($pre));

        $this->assertSame($expected, $post);
    }

    public function testStreetTypeSync()
    {
        $pre = ['house_number' => '23', 'street_name' => 'Monmouth', 'street_type' => 'st'];
        $expected = '23 monmouth street';

        $PSync = new PropertySync();
        $post_id = $this->invokeMethod($PSync, 'addressSync', array($pre));
        $post = \CityNexus\CityNexus\Property::find($post_id)->full_address;
        $this->assertSame($expected, $post);
    }


    public function testCheckForRawID()
    {
        $property['full_address'] = '23 monmouth st';

        $raw = \CityNexus\CityNexus\RawAddress::create(['address' => \GuzzleHttp\json_encode($property), 'property_id' => 1]);

        $PSync = new PropertySync();

        $post = $this->invokeMethod($PSync, 'checkForRawID', array(\GuzzleHttp\json_encode($property)));

        $this->assertSame(1, $post);

    }

    public function testSetHouseNumber()
    {
        $number = '1234';
        $PSync = new PropertySync();

        $post = $this->invokeMethod($PSync, 'setHouseNumber', array("1234"));
        $this->assertSame(['house_number' => '1234'], $post, 'Returns four digit address');

        $post = $this->invokeMethod($PSync, 'setHouseNumber', array("Main"));
        $this->assertEquals(['house_number' => null], $post, 'Zero address');

        $post = $this->invokeMethod($PSync, 'setHouseNumber', array('12-34'));
        $this->assertSame(['house_number' => '12'], $post);

    }

    public function testProcessStreetName()
    {
        $PSync = new PropertySync();

        $address = ['#5', 'st', 'michale', 'st'];
        $post = $this->invokeMethod($PSync, 'processStreetName', array($address));
        $expected = ['street_name' => 'saint michale', 'street_type' => 'street', 'unit' => '#5'];
        $this->assertSame($expected, $post, 'Address with a unit after house number and St. street');

        $address = ['st', 'michale', 'st', 'unit', '7'];
        $post = $this->invokeMethod($PSync, 'processStreetName', array($address));
        $expected = ['street_name' => 'saint michale', 'street_type' => 'street', 'unit' => 'unit 7'];
        $this->assertSame($expected, $post, 'Normal address with St. street');

        $address = ['michale', 'st', 'unit', '7'];
        $post = $this->invokeMethod($PSync, 'processStreetName', array($address));
        $expected = ['street_name' => 'michale', 'street_type' => 'street', 'unit' => 'unit 7'];
        $this->assertSame($expected, $post, 'Normal address');

        $address = ['michale', '#7'];
        $post = $this->invokeMethod($PSync, 'processStreetName', array($address));
        $expected = ['street_name' => 'michale', 'unit' => '#7'];
        $this->assertSame($expected, $post, 'Normal address without street type but #unit');

    }

    public function testFullAddressSync()
    {
        $raw_address = '23 St. Michale, #5';
        $property = \CityNexus\CityNexus\Property::create(['location_id' => '1', 'house_number' => '23', 'street_name' => 'saint michale', 'street_type' => 'street', 'unit' => '#5']);

        \CityNexus\CityNexus\RawAddress::create(['address' => json_encode(['full_address' => $raw_address]), 'property_id' => $property->id]);

        $psync = new PropertySync();
        $test = $psync->addressSync($raw_address);
        $this->assertSame($test, $property->id, "Test a raw full address match");

    }

    public function testArrayAddressSyncRaw()
    {

        $raw_address = ['house_number' => '23', 'street_name' => 'St. Michale St. #5'];

        $property = \CityNexus\CityNexus\Property::create(['location_id' => '1', 'house_number' => '23', 'street_name' => 'saint michale', 'street_type' => 'street', 'unit' => '#5']);

        \CityNexus\CityNexus\RawAddress::create(['address' => json_encode($raw_address), 'property_id' => $property->id]);
        $psync = new PropertySync();
        $test = $psync->addressSync($raw_address);
        $this->assertSame($test, $property->id, "Test a raw full address match");

        $raw_address = ['house_number' => '23', 'street_name' => 'St. Michale st #5'];
        $psync = new PropertySync();
        $test = $psync->addressSync($raw_address);
        $this->assertSame($test, $property->id, "Test a raw full address match");

    }

    public function testArrayAddressSyncNotRaw()
    {

        $raw_address = ['house_number' => '23', 'street_name' => 'St. Michale St. #5'];

        $property = \CityNexus\CityNexus\Property::create(['location_id' => '1', 'house_number' => '23', 'street_name' => 'saint michale', 'street_type' => 'street', 'unit' => '#5']);

        $raw_address = ['house_number' => '23', 'street_name' => 'St. Michale st #5'];
        $psync = new PropertySync();
        $test = $psync->addressSync($raw_address);
        $this->assertSame($test, $property->id, "Test a raw full address match");

    }

    public function testCheckForUnitInAddress()
    {
        $test = '23 #5';
        $expected = ['house_number' => '23', 'unit' => "#5"];
        $PSync = new PropertySync();
        $result = $this->invokeMethod($PSync, 'checkForUnitInAddress', array($test));

        $this->assertSame($result, $expected, 'House number and unit seperated by space returns array');


    }

    public function testProcessFullAddress()
    {
        $test = ['23', 'st', 'michale', 'st', '#5'];
        $expected = ['house_number' => '23', 'street_name' => 'saint michale', 'street_type' => 'street', 'unit' => "#5"];
        $PSync = new PropertySync();
        $result = $this->invokeMethod($PSync, 'processFullAddress', array($test));

        $this->assertSame($result, $expected, 'Process normal full address as array');

        $test = ['23', 'st', 'michale', '#5'];
        $expected = ['house_number' => '23', 'street_name' => 'saint michale', 'unit' => "#5"];
        $PSync = new PropertySync();
        $result = $this->invokeMethod($PSync, 'processFullAddress', array($test));

        $this->assertSame($result, $expected, 'Process normal full address as array with # address');

    }

}

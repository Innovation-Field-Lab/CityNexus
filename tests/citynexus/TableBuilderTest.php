<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CityNexus\CityNexus\TableBuilder;

class TableBuilderControllerTest extends TestCase
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

        $data = \GuzzleHttp\json_decode(json_encode(['house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street']));

        $property = \CityNexus\CityNexus\Property::create([
            'full_address' => '123 testing street',
            'house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street'
        ]);

        $result = $tableBulider->findSyncId('citynexus_properties', $data, $syncValues);

        $this->assertSame($property->id, $result);
    }

    /**
     *
     * Test Tabler Index page
     *
     */
    public function testFindSyncIDWithHypenedAddresses()
    {
        $tableBulider = new TableBuilder();

        $syncValues = [
            'house_number' => 'house_number',
            'street_name' => 'street_name',
            'street_type' => 'street_type'];

        $data = \GuzzleHttp\json_decode(json_encode(['house_number' => '1-23',
            'street_name' => 'testing',
            'street_type' => 'street']));

        $property = \CityNexus\CityNexus\Property::create([
            'full_address' => '1 testing street',
            'house_number' => '1',
            'street_name' => 'testing',
            'street_type' => 'street'
        ]);

        $result = $tableBulider->findSyncId('citynexus_properties', $data, $syncValues);

        $this->assertSame($property->id, $result);
    }

    /**
     *
     * Test Tabler Index page
     *
     */
    public function testFindSyncIDWithHypenedFullAddresses()
    {
        $tableBulider = new TableBuilder();

        $syncValues = [
            'full_address' => 'full_address'];

        $data = \GuzzleHttp\json_decode(json_encode(['full_address' => '1-23 testing street']));

        $property = \CityNexus\CityNexus\Property::create([
            'full_address' => '1 testing street',
            'house_number' => '1',
            'street_name' => 'testing',
            'street_type' => 'street'
        ]);


        $result = $tableBulider->findSyncId('citynexus_properties', $data, $syncValues);

        $this->assertSame($property->id, $result);
    }

    public function testUnitsInHouseNumbers()
    {
        $syncValues = [
            'house_number' => 'house_number',
            'street_name' => 'street_name',
            'street_type' => 'street_type'];

        $data = \GuzzleHttp\json_decode(json_encode(['house_number' => '123 #5',
            'street_name' => 'testing',
            'street_type' => 'street']));

        $property = \CityNexus\CityNexus\Property::create([
            'full_address' => '123 testing street #5',
            'house_number' => '123',
            'street_name' => 'testing',
            'street_type' => 'street',
            'unit' => '#5'
        ]);

        $tableBulider = new TableBuilder();

        $result = $tableBulider->findSyncId('citynexus_properties', $data, $syncValues);

        $this->assertSame($property->id, $result);
    }

//    public function testUploadProcessing()
//    {
//        $access = factory(App\User::class)->create(
//            [
//                'super_admin' => true
//            ]
//        );
//        $data = '{"excel":{},"reader":{},"file":{},"columns":[],"title":"phpWVyrnL","ext":"","encoding":false,"format":"CSV","calculate":true,"separator":false,"ignoreEmpty":false,"formatDates":true,"dateColumns":[],"noHeading":false,"dateFormat":false,"remembered":false,"cacheMinutes":10,"selectedSheets":[],"selectedSheetIndices":[],"filters":{"registered":{"chunk":"Maatwebsite\Excel\Filters\ChunkReadFilter"},"enabled":[]},"filesystem":{},"identifier":{"filesystem":{}},"parsed":[{"callnum":"11-2","datetime":"1\/1\/2011 1:06:00 AM","address":"POND ST","description":"M\/V PARKING"},{"callnum":"11-1","datetime":"1\/1\/2011 1:15:00 AM","address":"41 BOWDOIN ST","description":"MEDICAL AID"},{"callnum":"11-9","datetime":"1\/1\/2011 1:46:00 PM","address":"46 TRITON AVE","description":"PERSON CHECK"},{"callnum":"11-33","datetime":"1\/1\/2011 10:15:00 PM","address":"LINCOLN ST","description":"INTOXICATED SUBJECT(S)"},{"callnum":"11-34","datetime":"1\/1\/2011 11:20:00 PM","address":"CENTRAL ST","description":"SUSPICIOUS [SUBJECT\/S]"},{"callnum":"11-7","datetime":"1\/1\/2011 12:31:00 PM","address":"42 OVERLOOK DR","description":"PROPERTY CHECK"},{"callnum":"11-8","datetime":"1\/1\/2011 12:48:00 PM","address":"83 SOMERSET AVE","description":"VANDALISM"},{"callnum":"11-3","datetime":"1\/1\/2011 3:20:00 AM","address":"42D OVERLOOK DR","description":"E911"},{"callnum":"11-10","datetime":"1\/1\/2011 3:34:00 PM","address":"LOCUST ST","description":"M\/V PARKING"},{"callnum":"11-13","datetime":"1\/1\/2011 4:32:00 PM","address":"JEFFERSON ST","description":"DISTURBANCE (KIDS\/LOITERING)"}]}';
//        $table = \CityNexus\CityNexus\Table::create(['raw_upload' => $data]);
//        $this->actingAs($access)->visit(action('\CityNexus\CityNexus\Http\TablerController@getCreateScheme', [3]))
//            ->select('full_address', 'map[address][sync]')
//            ->type('Test Database', 'table_name')
//            ->press('Save and Complete Upload');
//    }

    public function testProcessUpload()
    {
        $tableContoller = new \CityNexus\CityNexus\Http\TablerController();
        $tabler = new TableBuilder();
        $now = '2016-04-16 20:14:15';

        $scheme = '{"id":{"show":"on","name":"ID","key":"id","type":"integer","sync":"","push":""},"year":{"show":"on","name":"Year","key":"year","type":"integer","sync":"","push":""},"ticket":{"show":"on","name":"Ticket","key":"ticket","type":"integer","sync":"","push":""},"issueddate":{"show":"on","name":"Issueddate","key":"issueddate","type":"datetime","sync":"","push":""},"time":{"show":"on","name":"Time","key":"time","type":"string","sync":"","push":""},"vision_id":{"show":"on","name":"Vision_id","key":"vision_id","type":"integer","sync":"","push":""},"point_id":{"show":"on","name":"Point_id","key":"point_id","type":"integer","sync":"","push":""},"map":{"show":"on","name":"Map","key":"map","type":"integer","sync":"","push":""},"block":{"show":"on","name":"Block","key":"block","type":"integer","sync":"","push":""},"lot":{"show":"on","name":"Lot","key":"lot","type":"integer","sync":"","push":""},"house":{"show":"on","name":"House","key":"house","type":"string","sync":"house_number","push":""},"location":{"show":"on","name":"Location","key":"location","type":"string","sync":"street_name","push":""},"owner":{"show":"on","name":"Owner","key":"owner","type":"string","sync":"","push":""},"owner2":{"show":"on","name":"Owner2","key":"owner2","type":"string","sync":"","push":""},"billing_address":{"show":"on","name":"Billing_address","key":"billing_address","type":"string","sync":"","push":""},"city":{"show":"on","name":"City","key":"city","type":"string","sync":"","push":""},"state":{"show":"on","name":"State","key":"state","type":"string","sync":"","push":""},"zip":{"show":"on","name":"Zip","key":"zip","type":"string","sync":"","push":""},"violations":{"show":"on","name":"Violations","key":"violations","type":"string","sync":"","push":""},"article":{"show":"on","name":"Article","key":"article","type":"string","sync":"","push":""},"section":{"show":"on","name":"Section","key":"section","type":"float","sync":"","push":""},"warning":{"show":"on","name":"Warning","key":"warning","type":"string","sync":"","push":""},"offense":{"show":"on","name":"Offense","key":"offense","type":"integer","sync":"","push":""},"fine":{"show":"on","name":"Fine","key":"fine","type":"integer","sync":"","push":""}}';

        $settings = json_encode(['timestamp' => null, 'property_id' => null, 'unique_id' => 'id']);
        $table['table_name'] = $tabler->create(json_decode(json_encode(['table_title' => "Process Upload Tabler Test", 'scheme' => $scheme, 'settings' => $settings ])));
        $table['scheme'] = $scheme;
        $table['settings'] = $settings;
        $table = \CityNexus\CityNexus\Table::create($table);

        $data[] = json_decode('{"id":"1","year":2016,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"TEST ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 GROVE ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}');
        $data[] = json_decode('{"id":"2","year":2015,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"GROVE ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 TEST ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}');
        $data[] = json_decode('{"id":"3","year":2015,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"GROVE ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 GROVE ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}');
        $data[] = json_decode('{"id":"3","year":2015,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"GROVE ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 GROVE ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}');
        $upload_id = 999999;

        $tableContoller->processUpload($table, $data, $upload_id);

        $result = \Illuminate\Support\Facades\DB::table('tabler_process_upload_tabler_test')->where('year', 2015)->count();

        $this->assertEquals(2, $result, 'Addition of three records');

        $result = \Illuminate\Support\Facades\DB::table('citynexus_locations')->where('full_address', '81 test street')->count();

        $this->assertEquals(1, $result, "Creation of location record");

    }
}

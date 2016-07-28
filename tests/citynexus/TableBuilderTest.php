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

        $data[] = json_decode('{"id":"1","year":2016,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"TEST ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 GROVE ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}', true);
        $data[] = json_decode('{"id":"2","year":2015,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"GROVE ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 TEST ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}', true);
        $data[] = json_decode('{"id":"3","year":2015,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"GROVE ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 GROVE ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}', true);
        $data[] = json_decode('{"id":"3","year":2015,"ticket":2345,"issueddate":"02\/26\/2016","time":"1:44 PM","vision_id":3071,"point_id":2371,"map":30,"block":0,"lot":41,"house":81,"location":"GROVE ST","owner":"ARIAS CLAUDIA","owner2":null,"billing_address":"81 GROVE ST","city":"CHELSEA","state":"MA","zip":"\'02150","violations":"Other","article":"C.O.","section":"6-2","warning":null,"offense":3,"fine":300}', true);
        $upload_id = 999999;

        $tableContoller->processUpload($table, $data, $upload_id);

        $result = \Illuminate\Support\Facades\DB::table('tabler_process_upload_tabler_test')->where('year', 2015)->count();

        $this->assertEquals(2, $result, 'Addition of three records');

        $result = \Illuminate\Support\Facades\DB::table('citynexus_locations')->where('full_address', '81 test street')->count();

        $this->assertEquals(1, $result, "Creation of location record");

    }

    public function testLeadingZeroIssues()
    {
        $tableBuilder = new TableBuilder();

        $table = factory(CityNexus\CityNexus\Table::class)->create([
            'scheme' => '{"rownum":{"show":"on","name":"Rownum","key":"rownum","type":"integer","sync":"","push":""},"cfs_no":{"show":"on","name":"Cfs_no","key":"cfs_no","type":"string","sync":"","push":""},"grid_pol":{"show":"on","name":"Grid_pol","key":"grid_pol","type":"string","sync":"","push":""},"town_code":{"show":"on","name":"Town_code","key":"town_code","type":"string","sync":"","push":""},"badge_no_primary":{"show":"on","name":"Badge_no_primary","key":"badge_no_primary","type":"string","sync":"","push":""},"primary_unit":{"show":"on","name":"Primary_unit","key":"primary_unit","type":"string","sync":"","push":""},"agency_pol":{"show":"on","name":"Agency_pol","key":"agency_pol","type":"string","sync":"","push":""},"act_call_type":{"show":"on","name":"Act_call_type","key":"act_call_type","type":"integer","sync":"","push":""},"call_description":{"show":"on","name":"Call_description","key":"call_description","type":"string","sync":"","push":""},"call_type":{"show":"on","name":"Call_type","key":"call_type","type":"integer","sync":"","push":""},"time_rec_tm":{"show":"on","name":"Time_rec_tm","key":"time_rec_tm","type":"string","sync":"","push":""},"disp_pol":{"show":"on","name":"Disp_pol","key":"disp_pol","type":"string","sync":"","push":""},"location":{"show":"on","name":"Location","key":"location","type":"string","sync":"full_address","push":""},"premise_name":{"show":"on","name":"Premise_name","key":"premise_name","type":"string","sync":"","push":""},"tag_no":{"show":"on","name":"Tag_no","key":"tag_no","type":"string","sync":"","push":""},"date_occured":{"show":"on","name":"Date_occured","key":"date_occured","type":"string","sync":"","push":""},"latitude":{"show":"on","name":"Latitude","key":"latitude","type":"string","sync":"","push":"lat"},"longitude":{"show":"on","name":"Longitude","key":"longitude","type":"string","sync":"","push":"long"},"call_type_tat_icon":{"show":"on","name":"Call_type_tat_icon","key":"call_type_tat_icon","type":"string","sync":"","push":""},"stop_nature":{"show":"on","name":"Stop_nature","key":"stop_nature","type":"string","sync":"","push":""},"stop_disposition":{"show":"on","name":"Stop_disposition","key":"stop_disposition","type":"string","sync":"","push":""},"city":{"show":"on","name":"City","key":"city","type":"string","sync":"","push":""},"dow":{"show":"on","name":"Dow","key":"dow","type":"string","sync":"","push":""},"statute":{"show":"on","name":"Statute","key":"statute","type":"string","sync":"","push":""},"statute2":{"show":"on","name":"Statute2","key":"statute2","type":"string","sync":"","push":""},"casedispo":{"show":"on","name":"Casedispo","key":"casedispo","type":"string","sync":"","push":""}}'
        ]);

        $tableBuilder->create($table);

        $rawUpload = '{"rownum":127,"cfs_no":"1500751931","grid_pol":null,"town_code":"T258","badge_no_primary":null,"primary_unit":null,"agency_pol":"P","act_call_type":140,"call_description":"DISTURBANCE-GENERAL ","call_type":140,"time_rec_tm":"3\/1\/15 0:34","disp_pol":null,"location":"00019 HAZEL ST","premise_name":null,"tag_no":null,"date_occured":"3\/1\/15 0:00","latitude":null,"longitude":null,"call_type_tat_icon":"p.png","stop_nature":null,"stop_disposition":null,"city":"Salem","dow":"Sunday","statute":null,"statute2":null,"casedispo":null}';

        $row = DB::table($table->table_name)->insertGetId(['upload_id' => 9999, 'raw' => $rawUpload, 'updated_at' => \Carbon\Carbon::now(), 'created_at' => \Carbon\Carbon::now()]);

        $tableBuilder->processRecord($row, $table->table_name);

        $result = DB::table($table->table_name)->where('id', $row)->first();

        $this->assertEquals(\CityNexus\CityNexus\Property::find($result->property_id)->house_number, 19);

    }

    public function testCleanName()
    {
        $rawname = "Hell's Bells!";
        $cleanname = 'hells_bells';
        $tabeBuilder = new TableBuilder();
        $result = $tabeBuilder->cleanName($rawname);

        $this->assertSame($cleanname, $result);
    }


    public function testWinOwnerOcc()
    {
        $tableBuilder = new TableBuilder();

        $table = factory(CityNexus\CityNexus\Table::class)->create([
            'scheme' => '{"number":{"show":"on","name":"Number","key":"number","type":"string","sync":"house_number","push":"null","meta":""},"street":{"show":"on","name":"Street","key":"street","type":"string","sync":"street_name","push":"null","meta":""},"0":{"skip":"on","name":"0","key":"0","type":"string","sync":"null","push":"null","meta":""},"owner":{"show":"on","name":"Owner","key":"owner","type":"string","sync":"null","push":"null","meta":""},"use_code":{"show":"on","name":"Use_code","key":"use_code","type":"string","sync":"null","push":"null","meta":""},"use_description":{"show":"on","name":"Use_description","key":"use_description","type":"string","sync":"null","push":"null","meta":""},"mailing_address":{"show":"on","name":"Mailing_address","key":"mailing_address","type":"string","sync":"null","push":"null","meta":""},"citytown":{"show":"on","name":"Citytown","key":"citytown","type":"string","sync":"null","push":"null","meta":""},"state":{"show":"on","name":"State","key":"state","type":"string","sync":"null","push":"null","meta":""},"zip_code":{"show":"on","name":"Zip_code","key":"zip_code","type":"string","sync":"null","push":"null","meta":""},"letter_sent":{"show":"on","name":"Letter_sent","key":"letter_sent","type":"datetime","sync":"null","push":"null","meta":""},"2nd_notice":{"show":"on","name":"2nd_notice","key":"2nd_notice","type":"datetime","sync":"null","push":"null","meta":""},"3rd_notice":{"show":"on","name":"3rd_notice","key":"3rd_notice","type":"datetime","sync":"null","push":"null","meta":""},"insp_date":{"show":"on","name":"Insp_date","key":"insp_date","type":"datetime","sync":"null","push":"null","meta":""},"results":{"show":"on","name":"Results","key":"results","type":"string","sync":"null","push":"null","meta":""},"email":{"show":"on","name":"Email","key":"email","type":"string","sync":"null","push":"null","meta":""},"phone":{"show":"on","name":"Phone","key":"phone","type":"string","sync":"null","push":"null","meta":""},"notes":{"show":"on","name":"Notes","key":"notes","type":"string","sync":"null","push":"null","meta":""}}']);
        $tableBuilder->create($table);

        $rawUpload = '{"number":547,"street":"FACE ST","0":null,"owner":"O\'MALLY WILLIAM W","use_code":1050,"use_description":"APT 3 UNIT","mailing_address":"83 TAFT RD","citytown":"WINTHROP","state":"MA","zip_code":2152,"letter_sent":"9\/15\/2014","2nd_notice":null,"3rd_notice":null,"insp_date":null,"results":"1 UNIT PASS","email":null,"phone":null,"notes":null}';

        $row = DB::table($table->table_name)->insertGetId(['upload_id' => 9999, 'raw' => $rawUpload, 'updated_at' => \Carbon\Carbon::now(), 'created_at' => \Carbon\Carbon::now()]);

        $tableBuilder->processRecord($row, $table->table_name);

        $results = DB::table($table->table_name)->where('id', $row)->first();
        $expected = [
            'number' => '547',
            'owner' => "O'MALLY WILLIAM W",
            'letter_sent' => "2014-09-15 00:00:00"
        ];

        $result = [
            'number' => $results->number,
            'owner' => $results->owner,
            'letter_sent' => $results->letter_sent
        ];

        $this->assertSame($expected, $result);
    }



        public function testTownHall()
        {
            $tableBuilder = new TableBuilder();

            $table = factory(CityNexus\CityNexus\Table::class)->create([
                'scheme' => '{"residential_address_street_number":{"show":"on","name":"Residential_address_street_number","key":"residential_address_street_number","type":"integer","sync":"house_number","push":"","meta":""},"residential_address_street_suffix":{"show":"on","name":"Residential_address_street_suffix","key":"residential_address_street_suffix","type":"string","sync":"","push":"","meta":""},"residential_address_street_name":{"show":"on","name":"Residential_address_street_name","key":"residential_address_street_name","type":"string","sync":"street_name","push":"","meta":""},"residential_address_apartment_number":{"show":"on","name":"Residential_address_apartment_number","key":"residential_address_apartment_number","type":"string","sync":"unit","push":"","meta":""},"precinct_number":{"show":"on","name":"Precinct_number","key":"precinct_number","type":"integer","sync":"","push":"","meta":""}}'
                ]);
            $tableBuilder->create($table);

            $rawUpload = '{"residential_address_street_number":4,"residential_address_street_suffix":null,"residential_address_street_name":"ATLANTIC ST","residential_address_apartment_number":1,"precinct_number":1}';

            $row = DB::table($table->table_name)->insertGetId(['upload_id' => 9999, 'raw' => $rawUpload, 'updated_at' => \Carbon\Carbon::now(), 'created_at' => \Carbon\Carbon::now()]);

            $tableBuilder->processRecord($row, $table->table_name);

            $results = DB::table($table->table_name)->where('id', $row)->first();
            $property = \CityNexus\CityNexus\Property::find($results->property_id);

        }

    public function testPoliceData()
    {
        $tableBuilder = new TableBuilder();

        $table = factory(CityNexus\CityNexus\Table::class)->create([
            'scheme' => '{"callnum":{"show":"on","name":"Callnum","key":"callnum","type":"string","sync":"null","push":"null","meta":""},"datetime":{"show":"on","name":"Datetime","key":"datetime","type":"datetime","sync":"null","push":"null","meta":""},"address":{"show":"on","name":"Address","key":"address","type":"string","sync":"full_address","push":"null","meta":""},"description":{"show":"on","name":"Description","key":"description","type":"string","sync":"null","push":"null","meta":""}}'
        ]);
        $tableBuilder->create($table);

        $rawUpload = '{"callnum":"11-2","datetime":"1\/1\/2011 1:06:00 AM","address":"POND ST","description":"M\/V PARKING"}';

        $row = DB::table($table->table_name)->insertGetId(['upload_id' => 9999, 'raw' => $rawUpload, 'updated_at' => \Carbon\Carbon::now(), 'created_at' => \Carbon\Carbon::now()]);

        $tableBuilder->processRecord($row, $table->table_name);

        $results = DB::table($table->table_name)->where('id', $row)->first();
        $property = \CityNexus\CityNexus\Property::find($results->property_id);

    }

    public function testZeroAddress()
    {
        $tableBuilder = new TableBuilder();

        $table = factory(CityNexus\CityNexus\Table::class)->create([
            'scheme' => '{"callnum":{"show":"on","name":"Callnum","key":"callnum","type":"string","sync":"null","push":"null","meta":""},"datetime":{"show":"on","name":"Datetime","key":"datetime","type":"datetime","sync":"null","push":"null","meta":""},"address":{"show":"on","name":"Address","key":"address","type":"string","sync":"full_address","push":"null","meta":""},"description":{"show":"on","name":"Description","key":"description","type":"string","sync":"null","push":"null","meta":""}}'
        ]);
        $tableBuilder->create($table);

        $rawUpload = '{"callnum":"11-2","datetime":"1\/1\/2011 1:06:00 AM","address":"POND ST","description":"M\/V PARKING"}';

        $row = DB::table($table->table_name)->insertGetId(['upload_id' => 9999, 'raw' => $rawUpload, 'updated_at' => \Carbon\Carbon::now(), 'created_at' => \Carbon\Carbon::now()]);

        $tableBuilder->processRecord($row, $table->table_name);

        $results = DB::table($table->table_name)->where('id', $row)->first();

        $this->assertSame(null, $results->property_id);


    }

    public function testLaw()
    {
        $tableBuilder = new TableBuilder();

        $table = factory(CityNexus\CityNexus\Table::class)->create([
            'scheme' => '{"ref":{"show":"on","name":"Ref","key":"ref","type":"integer","sync":"null","push":"null","meta":""},"number":{"show":"on","name":"Number","key":"number","type":"integer","sync":"house_number","push":"null","meta":""},"address_of_building":{"show":"on","name":"Address_of_building","key":"address_of_building","type":"string","sync":"street_name","push":"null","meta":""},"unit":{"show":"on","name":"Unit","key":"unit","type":"string","sync":"unit","push":"null","meta":""},"date_entered":{"show":"on","name":"Date_entered","key":"date_entered","type":"datetime","sync":"null","push":"null","meta":""},"name_of_loc_prop_man":{"show":"on","name":"Name_of_loc_prop_man","key":"name_of_loc_prop_man","type":"string","sync":"null","push":"null","meta":""},"company":{"show":"on","name":"Company","key":"company","type":"string","sync":"null","push":"null","meta":""},"address_of_loc_prop_man":{"show":"on","name":"Address_of_loc_prop_man","key":"address_of_loc_prop_man","type":"string","sync":"null","push":"null","meta":""},"suite":{"show":"on","name":"Suite","key":"suite","type":"string","sync":"null","push":"null","meta":""},"city":{"show":"on","name":"City","key":"city","type":"string","sync":"null","push":"null","meta":""},"state":{"show":"on","name":"State","key":"state","type":"string","sync":"null","push":"null","meta":""},"zip_code":{"show":"on","name":"Zip_code","key":"zip_code","type":"string","sync":"null","push":"null","meta":""},"changed_ownership":{"show":"on","name":"Changed_ownership","key":"changed_ownership","type":"string","sync":"null","push":"null","meta":""}}'
        ]);
        $tableBuilder->create($table);

        $rawUpload = '{"ref":22,"number":null,"address_of_building":null,"unit":null,"date_entered":null,"name_of_loc_prop_man":null,"company":null,"address_of_loc_prop_man":null,"suite":null,"city":null,"state":null,"zip_code":null,"changed_ownership":null}';

        $row = DB::table($table->table_name)->insertGetId(['upload_id' => 9999, 'raw' => $rawUpload, 'updated_at' => \Carbon\Carbon::now(), 'created_at' => \Carbon\Carbon::now()]);

        $tableBuilder->processRecord($row, $table->table_name);

        $results = DB::table($table->table_name)->where('id', $row)->first();

        $this->assertSame(null, $results->property_id);


    }
}

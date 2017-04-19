<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TablerControllerTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;

//    /**
//     *
//     * Test Tabler Index page
//     *
//     */
//    public function testGetIndex()
//    {
//        $this->visit('/' . config('citynexus.tabler_root') . '/')
//        ->see('All Data Sets');
//    }
//
//    public function testGetUploader()
//    {
//        $this->visit('/' . config('citynexus.tabler_root') . '/uploader')
//            ->see('Create Data Set From Upload');
//    }
//
//    /**
//     *
//     * Generate a scheme based on the raw data
//     *
//     */
//    public function testGetCreateScheme()
//    {
//        $table = new \CityNexus\CityNexus\Table();
//        $table->raw_upload = '[
//  {
//    "avpid": null,
//    "map": 55,
//    "lot": 32,
//    "usecode": 1010,
//    "sitidx": 5,
//    "nhbd": 1010,
//    "location": "213 broadway pl",
//    "saledate": null,
//    "price": 100000,
//    "valcode": null,
//    "styledesc": null,
//    "occupancy": 3,
//    "numbedrm": null,
//    "bldgarealiving": 1234,
//    "bldgareagross": 2345,
//    "landareainacres": 0.0123,
//    "ah": null,
//    "landvalue": 44444,
//    "bldgvalue": 333333,
//    "parcelvalue": 455553,
//    "type": "Residential123",
//    "bookpage": null,
//    "grantee": null,
//    "co_granteesname": null,
//    "mailingaddress": "211 Broadway RW 405",
//    "city": null,
//    "state": null,
//    "zip": null,
//    "l": null,
//    "complexdesc": "211 Broadway",
//    "style": "Condo-Apt",
//    "sf": 700
//  }
//]';
//        $table->save();
//        $this->visit('/' . config('citynexus.tabler_root') . '/create-scheme/' . $table->id)
//        ->see('bldgarealiving');
//    }
//
//
//    /**
//     *
//     * Test that there is an upload page for "Test Table Upload"
//     *
//     */
//    public function testGetNewUpload()
//    {
//        $table = \CityNexus\CityNexus\Table::create(['table_title' => 'Test Table', 'scheme' => '{"tt":{"show":"true","name":"Tt","type":"string","key":"tt","sync":"","push":""},"account":{"show":"true","name":"Account","type":"string","key":"account","sync":"","push":""},"location":{"show":"true","name":"Location","type":"string","key":"location","sync":"full_address","push":""},"tax":{"show":"true","name":"Tax","type":"float","key":"tax","sync":"","push":""},"betterments":{"show":"true","name":"Betterments","type":"float","key":"betterments","sync":"","push":""},"interest":{"show":"true","name":"Interest","type":"float","key":"interest","sync":"","push":""},"demand":{"show":"true","name":"Demand","type":"float","key":"demand","sync":"","push":""},"charges":{"show":"true","name":"Charges","type":"float","key":"charges","sync":"","push":""},"trs_paid_interest":{"show":"true","name":"Trs Paid Interest","type":"float","key":"trs_paid_interest","sync":"","push":""},"credits":{"show":"true","name":"Credits","type":"float","key":"credits","sync":"","push":""},"bal_due":{"show":"true","name":"Bal Due","type":"float","key":"bal_due","sync":"","push":""},"trs_chgs":{"show":"true","name":"Trs Chgs","type":"float","key":"trs_chgs","sync":"","push":""},"trs_accrued_interest":{"show":"true","name":"Trs Accrued Interest","type":"float","key":"trs_accrued_interest","sync":"","push":""},"total_due":{"show":"true","name":"Total Due","type":"float","key":"total_due","sync":"","push":""},"lat":{"show":"true","name":"Lat","type":"float","key":"lat","sync":"","push":"lat"},"longit":{"show":"true","name":"Longit","type":"float","key":"longit","sync":"","push":"long"},"geomerge":{"name":"Geomerge","type":"string","key":"geomerge","sync":"","push":""},"geostatus":{"name":"Geostatus","type":"string","key":"geostatus","sync":"","push":""},"city":{"name":"City","type":"string","key":"city","sync":"","push":""},"state":{"name":"State","type":"string","key":"state","sync":"","push":""},"database":{"name":"Database","type":"string","key":"database","sync":"","push":""},"geoid":{"name":"Geoid","type":"integer","key":"geoid","sync":"","push":""}}']);
//
//        $this->visit('/' . config('citynexus.tabler_root') . '/new-upload/' . $table->id)
//            ->see('<b>Test Table</b> Upload');
//    }
//
//
//    public function testBreakUpAddress()
//    {
//        $address = '123-23 Franklin Road, Apt 1s';
//        $expected = ['123-23', 'Franklin', 'Road', 'Apt', '1s'];
//
//        $output = \CityNexus\CityNexus\TableBuilder::breakUpAddress( $address );
//
//        assertSame($expected, $output);
//    }

    public function testGetDemergeProperty()
    {
        $property = \CityNexus\CityNexus\Property::create([]);
        $alias = \CityNexus\CityNexus\Property::create(['alias_of' => $property->id]);

        $this->visit('/tabler/demerge-property/' . $alias->id);

        $alias = \CityNexus\CityNexus\Property::find($alias->id);

        $this->assertEquals(null, $alias->alias_of);
    }

    public function testUniqueIdUpload()
    {
        // Bring in helpers
        $tabler = new \CityNexus\CityNexus\Http\TablerController();
        $builder = new \CityNexus\CityNexus\TableBuilder();


        // Create fake table.
        $table = \CityNexus\CityNexus\Table::create([
            'table_title' => 'Test Table',
            'scheme' => json_encode([
                'unique_id' => [
                    'show' => 'on',
                    'name' => 'Unique Id',
                    'key' => 'unique_id',
                    'type' => 'integer',
                    'sync' => '',
                    'push' => '',
                    'meta' => ''
                ],
                'data' => [
                    'show' => 'on',
                    'name' => 'Data',
                    'key' => 'data',
                    'type' => 'string',
                    'sync' => '',
                    'push' => '',
                    'meta' => ''
                ]
            ]),
            'settings' => json_encode([
                'unique_id' => 'unique_id'
            ])
        ]);

        $builder->create($table);

        // Create upload record
        $upload = \CityNexus\CityNexus\Upload::create([
            'table_id' => $table->id,
            'note' => 'test',
        ]);

        // Add singe fake row.
        \Illuminate\Support\Facades\DB::table($table->table_name)
            ->insert(
                ['unique_id' => 1,
                    'data' => 'oh, hello',
                    'upload_id' => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()]);


        // create sample data with one over lap
        $data = [
            [
                'unique_id' => 1,
                'data' => 'hey there'
            ],
            [
                'unique_id' => 2,
                'data' => 'hey there'
            ],
            [
                'unique_id' => 3,
                'data' => 'hey there'
            ],
        ];

        // send to the processer function
        $tabler->processUpload($table, $data, $upload->id);


        // Check what is in the DB
        $results = \Illuminate\Support\Facades\DB::table($table->table_name)->get();

        // Test that there are only three items in db
        $this->assertSame(count($results), 3);
    }


}

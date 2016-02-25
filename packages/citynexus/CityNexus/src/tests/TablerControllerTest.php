<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TablerControllerTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;

    /**
     *
     * Test Tabler Index page
     *
     */
    public function testGetIndex()
    {
        $this->visit('/' . config('citynexus.tabler_root') . '/')
        ->see('All Data Sets');
    }

    public function testGetUploader()
    {
        $this->visit('/' . config('citynexus.tabler_root') . '/uploader')
            ->see('Create Data Set From Upload');
    }

    /**
     *
     * Generate a scheme based on the raw data
     *
     */
    public function testGetCreateScheme()
    {
        $table = new \CityNexus\CityNexus\Table();
        $table->raw_upload = '[
  {
    "avpid": null,
    "map": 55,
    "lot": 32,
    "usecode": 1010,
    "sitidx": 5,
    "nhbd": 1010,
    "location": "213 broadway pl",
    "saledate": null,
    "price": 100000,
    "valcode": null,
    "styledesc": null,
    "occupancy": 3,
    "numbedrm": null,
    "bldgarealiving": 1234,
    "bldgareagross": 2345,
    "landareainacres": 0.0123,
    "ah": null,
    "landvalue": 44444,
    "bldgvalue": 333333,
    "parcelvalue": 455553,
    "type": "Residential123",
    "bookpage": null,
    "grantee": null,
    "co_granteesname": null,
    "mailingaddress": "211 Broadway RW 405",
    "city": null,
    "state": null,
    "zip": null,
    "l": null,
    "complexdesc": "211 Broadway",
    "style": "Condo-Apt",
    "sf": 700
  }
]';
        $table->save();
        $this->visit('/' . config('citynexus.tabler_root') . '/create-scheme/' . $table->id)
        ->see('bldgarealiving');
    }


}

<?php

use Illuminate\Database\Seeder;

class settings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \CityNexus\CityNexus\Setting::create([
            'id' => 'street_types',
            'type' => 'pairs',
            'setting' => json_encode(["st" => "street", "str"=> "street", "street" => "street", "strt" => "street", "parkway" => "parkway", "parkwy" => "parkway", "pkway" => "parkway", "pkwy" => "parkway", "pky" => "parkway", "exp" => "expressway", "expr" => "expressway", "express" => "expressway", "expressway" => "expressway", "expw" => "expressway", "expy" => "expressway", "dr" => "drive", "driv" => "drive", "drive" => "drive", "drv" => "drive", "cir" => "circle", "circ" => "circle", "circl" => "circle", "circle" => "circle", "crcl" => "circle", "crcle" => "circle", "blvd" => "boulevard", "boul" => "boulevard", "boulevard" => "boulevard", "boulv" => "boulevard", "av" => "avenue", "ave" => "avenue", "aven" => "avenue", "avenu" => "avenue", "avenue" => "avenue", "avn" => "avenue", "avnue" => "avenue", "allee" => "alley", "alley" => "alley", "ally" => "alley", "aly" => "alley", "ct" => "court", "court" => "court", 'row' => 'row', 'rw' => 'row', 'road' => 'road', 'rd' => 'road', 'way' => 'way', 'place' => 'place', 'plc' => 'place'])
        ]);
    }
}

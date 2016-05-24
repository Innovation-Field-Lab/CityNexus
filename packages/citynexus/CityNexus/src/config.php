<?php



return [

    // root directory
    'root_directory' => 'citynexus',

    'app_name' => env('CITYNEXUS_NAME', 'CityNexus'),
    'slogan' => env('CITYNEXUS_SLOGAN', 'Municipal Data Centralization'),

    // Layout variables
    "template" => 'citynexus::master.main',
    "section" => "main",

    // City information
    "city_state" => env('CITY_STATE', ''),
    "city" => env('CITY', ''),
    "state" => env('STATE', ''),

    // The route directory where the tabler routes will be hosted

    "tabler_root" => "tabler",

    // Sync points

    "sync" => [
        'property_id' => 'Property ID',
        'house_number' => 'House Number',
        'street_name' => 'Street Name',
        'unit' => 'Unit',
        'full_address' => 'Full Address',
    ],

    // Push data point to master file

    "push" => [
        'lat' => 'Latitude',
        'long' => 'Longitude',
    ],

    // Index field

    'index_id' => 'property_id',

    // Index table

    'index_table' => 'citynexus_properties',

    // Map Location

    'map_lat' => env('MAP_LAT', null),
    'map_long' => env('MAP_LONG', null),
    'map_zoom' => env('MAP_ZOOM', null),
    'gmap_api' => env('GMAP_API', null),

    // Address matching arrarys

    'street_types' => ["st" => "street", "str"=> "street", "street" => "street", "strt" => "street", "parkway" => "parkway", "parkwy" => "parkway", "pkway" => "parkway", "pkwy" => "parkway", "pky" => "parkway", "exp" => "expressway", "expr" => "expressway", "express" => "expressway", "expressway" => "expressway", "expw" => "expressway", "expy" => "expressway", "dr" => "drive", "driv" => "drive", "drive" => "drive", "drv" => "drive", "cir" => "circle", "circ" => "circle", "circl" => "circle", "circle" => "circle", "crcl" => "circle", "crcle" => "circle", "blvd" => "boulevard", "boul" => "boulevard", "boulevard" => "boulevard", "boulv" => "boulevard", "av" => "avenue", "ave" => "avenue", "aven" => "avenue", "avenu" => "avenue", "avenue" => "avenue", "avn" => "avenue", "avnue" => "avenue", "allee" => "alley", "alley" => "alley", "ally" => "alley", "aly" => "alley", "ct" => "court", "court" => "court", 'row' => 'row', 'rw' => 'row', 'road' => 'road', 'rd' => 'road', 'way' => 'way', 'place' => 'place', 'plc' => 'place', 'pl' => 'place', 'square' => 'square', 'terrace' => 'terrace', 'wy' => 'way', 'ln' => 'lane', ],
    'unit_types' => ['unit' => 'unit', '#' => '#', 'ut' => 'unit', 'apt' => 'apartment', 'apartment' => 'apartment', 'no' => 'number', 'number' => 'number', 'lot' => 'lot'],
];
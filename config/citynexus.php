<?php

return [

    // root directory
    'root_directory' => 'citynexus',

    // Layout variables
    "template" => 'layout.master',
    "section" => "main",

    // CityNexus will expects corresponding booleans in the user model.
    // Additionally, CityNexus will expect an Admin boolean on the
    // User model with full viewing privileges unless specifically
    // excluded.

    'user_type' => [
        "PoliceDept",
        'MayorOffice'

    ],

    'data_sources' => [

        // Data structure scheme

        // data name
            //"info" = Settings for the dataset
                //["name"] => String of name to be used throughout application
                //["edit"] => Array of user types with edit privileges
                //["view"] => Array of user types with raw data viewing privileges
                //["hidden"] => Array of uses types excluded from viewing data
            //"data"
                //Name of field being imported lower cased and with underscores as spaces
                    //["maps_to"] => String name of the field in the database
                    //["type"] => data type: "string", "datetime", "boolean",
                        // "text", "integer", ["decimal" => [precision, scale]]

        'police' =>
        [   'info' => [
            'name' => 'Police Data',
            'edit' => [
                'PoliceDept'
            ],
            'view' => [
                'MayorOffice'
            ]
            ],
            'data' => [
            'date' =>
                ['maps_to' => 'created_at', 'type' => 'datetime'],
            'calls_for_service' =>
                ['maps_to' => 'calls_for_service', 'type' => 'integer'],
            'reports_filed' =>
                ['maps_to' => 'reports_filed', 'type' => 'integer']
            ]
        ]
    ],

    // The route directory where the tabler routes will be hosted

    "tabler_root" => "tabler",

    // Sync points

    "sync" => [
        'property_id' => 'Property ID',
        'house_number' => 'House Number',
        'street_name' => 'Street Name',
        'unit' => 'Unit',
        'full_address' => 'Full Address'
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

    // Address matching arrarys

    'street_types' => ["st" => "street", "str"=> "street", "street" => "street", "strt" => "street", "parkway" => "parkway", "parkwy" => "parkway", "pkway" => "parkway", "pkwy" => "parkway", "pky" => "parkway", "exp" => "expressway", "expr" => "expressway", "express" => "expressway", "expressway" => "expressway", "expw" => "expressway", "expy" => "expressway", "dr" => "drive", "driv" => "drive", "drive" => "drive", "drv" => "drive", "cir" => "circle", "circ" => "circle", "circl" => "circle", "circle" => "circle", "crcl" => "circle", "crcle" => "circle", "blvd" => "boulevard", "boul" => "boulevard", "boulevard" => "boulevard", "boulv" => "boulevard", "av" => "avenue", "ave" => "avenue", "aven" => "avenue", "avenu" => "avenue", "avenue" => "avenue", "avn" => "avenue", "avnue" => "avenue", "allee" => "alley", "alley" => "alley", "ally" => "alley", "aly" => "alley", "ct" => "court", "court" => "court", 'row' => 'row', 'rw' => 'row', 'road' => 'road', 'rd' => 'road', 'way' => 'way', 'place' => 'place', 'plc' => 'place', 'pl' => 'place', 'square' => 'square', 'terrace' => 'terrace', 'wy' => 'way', 'ln' => 'lane', ],
    'unit_types' => ['unit' => 'unit', '#' => '#', 'ut' => 'unit', 'apt' => 'apartment', 'apartment' => 'apartment', 'no' => 'number', 'number' => 'number', 'lot' => 'lot'],
];
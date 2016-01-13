<?php

return [

    // The route directory where the tabler routes will be hosted

    "root_directory" => "tabler",

    // Layout variables

    "template" => 'layout.master',

    "section" => "main",

    // Sync points

    "sync" => [
        'property_id' => 'Property ID',
        'house_number' => 'House Number',
        'street_name' => 'Street Name',
        'unit' => 'Unit',
        'full_address' => 'Full Address'
    ],

    // Index field

    'index_id' => 'property_id',

    // Index table

    'index_table' => 'citynexus_properties',

];
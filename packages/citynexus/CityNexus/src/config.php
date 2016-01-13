<?php

return [

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
    ]
];
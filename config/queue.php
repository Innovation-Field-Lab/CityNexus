<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd",
    |            "sqs", "iron", "redis"
    |
    */

    'default' => env('QUEUE_DRIVER', 'beanstalkd'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'expire' => 60,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host'   => 'localhost',
            'queue'  => 'default',
            'ttr'    => 60,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key'    => 'your-public-key',
            'secret' => 'your-secret-key',
            'queue'  => 'your-queue-url',
            'region' => 'us-east-1',
        ],

        'iron' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-eu-west-1-1.iron.io',
            'token'   => 'WtyjV2jx7YARnaCQ7fxa',
            'project' => '56dc83e9ac66fa000600010b',
            'queue'   => 'citynexus-cn',
            'encrypt' => true,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'pub-redis-12126.us-east-1-2.1.ec2.garantiadata.com:12126',
            'password' => 'iT43cxg9qjoTXEd0',
            'queue'  => 'default',
            'expire' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => 'mysql', 'table' => 'failed_jobs',
    ],

];

<?php

/**
 * This file is part of the GeocoderLaravel library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    // Providers get called in the chain order given here.
    // The first one to return a result will be used.
    'providers' => [
        'Geocoder\Provider\GoogleMapsProvider' => ['en-US', 'US', true],
        '\Geocoder\Provider\GoogleMapsBusinessProvider' => ['en-US', env('GCLIENT_ID'), env('GMAPI_KEY'), 'US', true, true]
    ],
    'adapter'  => 'Geocoder\HttpAdapter\CurlHttpAdapter',
];

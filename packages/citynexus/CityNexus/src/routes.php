<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/jobs', function(){
    $jobs = \Illuminate\Support\Facades\DB::table('jobs')->get();
    return var_dump($jobs);
});

Route::get('/jobs/clear-all', function(){
    $jobs = \Illuminate\Support\Facades\DB::table('jobs')->get();
        foreach($jobs as $job)
        {
            DB::table('jobs')->where('id', $job->id)->delete();
        }
    return var_dump($jobs);
});


Route::group(['middleware' => 'auth', 'prefix' => config('citynexus.root_directory') . '/risk-score' ], function() {

    Route::controller('/', 'CityNexus\CityNexus\Http\RiskScoreController');
});

Route::group(['middleware' => 'auth', 'prefix' => config('citynexus.root_directory') ], function() {

    Route::controller('/settings', 'CityNexus\CityNexus\Http\CitynexusSettingsController');
    Route::controller('/notes/', 'CityNexus\CityNexus\Http\NoteController');
    Route::controller('/', 'CityNexus\CityNexus\Http\CitynexusController');

});

// Tabler Features

Route::group(['middleware' => 'auth', 'prefix' => config('citynexus.tabler_root') ], function() {

Route::controller('/', 'CityNexus\CityNexus\Http\TablerController');

});

Route::group(['middleware' => 'auth'], function() {

    Route::get('/', 'CityNexus\CityNexus\Http\CityNexusController@getIndex');


});
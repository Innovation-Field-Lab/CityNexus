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

Route::group(['middleware' => 'auth', 'prefix' => config('citynexus.root_directory') ], function() {


    Route::get('/property', 'CityNexus\CityNexus\Http\CitynexusController@getProperty');
    Route::get('/properties', 'CityNexus\CityNexus\Http\CitynexusController@getProperties');
    Route::get('/properties-data', 'CityNexus\CityNexus\Http\CitynexusController@getPropertiesData');


// Score Builder

    Route::get('/risk-score/scores', 'CityNexus\CityNexus\Http\CitynexusController@getScores');

    Route::get('/risk-score/new', 'CityNexus\CityNexus\Http\CitynexusController@getRiskscoreCreate');

    Route::post('/risk-score/update-score', 'CityNexus\CityNexus\Http\CitynexusController@postUpdateScore');

    Route::get('/risk-score/data-fields', 'CityNexus\CityNexus\Http\CitynexusController@getRiskscoreDatafields');

    Route::get('/risk-score/data-field', 'CityNexus\CityNexus\Http\CitynexusController@getRiskscoreDatafield');

    Route::get('/risk-score/create-element', 'CityNexus\CityNexus\Http\CitynexusController@getCreateElement');

    Route::post('/risk-score/save-score', 'CityNexus\CityNexus\Http\CitynexusController@postSaveScore');

    Route::get('/risk-score/edit-score', 'CityNexus\CityNexus\Http\CitynexusController@getEditScore');

    Route::get('/risk-score/duplicate-score', 'CityNexus\CityNexus\Http\CitynexusController@getDuplicateScore');

    Route::get('/risk-score/generate-score', 'CityNexus\CityNexus\Http\CitynexusController@getGenerateScore');

    Route::get('/risk-score/heat-map', 'CityNexus\CityNexus\Http\CitynexusController@getRiskscoreHeatmap');

});

// Tabler Features

Route::group(['middleware' => 'auth', 'prefix' => config('tabler.root_directory') ], function() {

Route::controller('/', 'CityNexus\CityNexus\Http\TablerController');

//    Route::get('/uploader', 'CityNexus\CityNexus\Http\TablerController@getUploader');
//    Route::post('/uploader', 'CityNexus\CityNexus\Http\TablerController@postUploader');
//
//    Route::get('/create-scheme/', 'CityNexus\CityNexus\Http\TablerController@getCreateScheme');
//    Route::post('/create-scheme', 'CityNexus\CityNexus\Http\TablerController@postCreateScheme');
//
//    Route::get('/new-upload/', 'CityNexus\CityNexus\Http\TablerController@getNewUpload');
//    Route::post('/new-upload/', 'CityNexus\CityNexus\Http\TablerController@postNewUpload');
//
//    Route::get('/edit-table/', 'CityNexus\CityNexus\Http\TablerController@getEditTable');
//    Route::post('/update-table/', 'CityNexus\CityNexus\Http\TablerController@postUpdateTable');
//
//    Route::get('/', 'CityNexus\CityNexus\Http\TablerController@getIndex');

});


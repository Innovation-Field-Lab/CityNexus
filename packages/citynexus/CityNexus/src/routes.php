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

Route::get( config('citynexus.root_directory') . '/property', 'CityNexus\CityNexus\Http\CitynexusController@getProperty');

Route::get( config('citynexus.root_directory') . '/properties', 'CityNexus\CityNexus\Http\CitynexusController@getProperties');
Route::get( config('citynexus.root_directory') . '/properties-data', 'CityNexus\CityNexus\Http\CitynexusController@getPropertiesData');


// Score Builder

Route::get( config('citynexus.root_directory') . '/risk-score/new', 'CityNexus\CityNexus\Http\CitynexusController@getRiskscoreCreate');

Route::get( config('citynexus.root_directory') . '/risk-score/data-fields', 'CityNexus\CityNexus\Http\CitynexusController@getRiskscoreDatafields');

Route::get( config('citynexus.root_directory') . '/risk-score/data-field', 'CityNexus\CityNexus\Http\CitynexusController@getRiskscoreDatafield');

Route::post( config('citynexus.root_directory') . '/risk-score/create-element', 'CityNexus\CityNexus\Http\CitynexusController@postCreateElement');
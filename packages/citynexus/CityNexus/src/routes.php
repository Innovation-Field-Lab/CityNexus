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

Route::controller("api-query", '\CityNexus\CityNexus\Http\APIController');

Route::controller("/citynexus/help", '\CityNexus\CityNexus\Http\HelpController');

Route::get('/activate-account/', function()
{
    $token = $_GET['key'];


    if($user = \App\User::where('activation', $token)->first())
    {
        return view('citynexus::email.create-password')->with('token', $token);
    }
    else
    {
        return response('Permission Denied', 403);
    }
});

Route::post('/activate-account', function()
{
    $password = $_POST['password'];
    $confirm = $_POST['confirm-password'];
    $token = $_POST['token'];
    if($user = \App\User::where('activation', $token)->first())
    {
        if($password == $confirm)
        {
            if(strlen($password) < 8)
            {
                \Illuminate\Support\Facades\Session::flash('flash_warning', "Password must be at least 8 characters");
                return redirect()->back();
            }
            $user->activation = null;
            $user->fill([
                'password' => Hash::make($password)
            ])->save();

            \Illuminate\Support\Facades\Auth::loginUsingId( $user->id );

            return redirect('/');
        }
        else
        {
            \Illuminate\Support\Facades\Session::flash('flash_warning', "Passwords didn't match");
            return redirect()->back();
        }
    }
});


Route::group(['middleware' => 'auth', 'prefix' => config('citynexus.root_directory') ], function() {
    Route::controller('/search', 'CityNexus\CityNexus\Http\SearchController');
    Route::controller('/risk-score', 'CityNexus\CityNexus\Http\RiskScoreController');
    Route::controller('/admin', 'CityNexus\CityNexus\Http\AdminController');
    Route::controller('/settings', 'CityNexus\CityNexus\Http\CitynexusSettingsController');
    Route::controller('/notes', 'CityNexus\CityNexus\Http\NoteController');
    Route::controller('/tags', 'CityNexus\CityNexus\Http\TagController');
    Route::controller('/reports/views', 'CityNexus\CityNexus\Http\ViewController');
    Route::controller('/reports', 'CityNexus\CityNexus\Http\ReportController');
    Route::controller('/property', 'CityNexus\CityNexus\Http\PropertyController');
    Route::controller('/task', 'CityNexus\CityNexus\Http\TaskController');
    Route::controller('/image', 'CityNexus\CityNexus\Http\ImageController');
    Route::controller('/widget', 'CityNexus\CityNexus\Http\WidgetController');
    Route::controller('/', 'CityNexus\CityNexus\Http\CitynexusController');

});

// Tabler Features

Route::group(['middleware' => 'auth', 'prefix' => config('citynexus.tabler_root') ], function() {

Route::controller('/', 'CityNexus\CityNexus\Http\TablerController');

});

Route::group(['middleware' => 'auth'], function() {

    Route::get('/', 'CityNexus\CityNexus\Http\CityNexusController@getIndex');


});

<?php

use App\User;
use App\Category;

Route::get('/', function ()
{
    return view('/homeIndex');
});

Route::get('/connections', function ()
{
    return view('/dashboard.connections');
});

//Auth::routes();//everything related to login/regisration/passreset/logout
Route::get('/register','RegistrationController@create');
Route::get('/activation','RegistrationController@activation');
Route::post('/register', 'RegistrationController@register')->name('register');

Route::get('/login','SessionsController@create');
Route::post('/login','SessionsController@store');
Route::get('/logout','SessionsController@destroy');
Route::get('/forgotPassword','SessionsController@reset');
Route::get('/reclaim','SessionsController@reclaim');

//DASHBOARD ROUTES
Route::get('/dashboard', 'DashboardController@index');
Route::get('/account/{User}', 'DashboardController@account');
Route::get('/notifications/{User}', 'DashboardController@notifications');
Route::get('/inbox/{inboxOwner}','DashboardController@inbox');
Route::get('/management/{User}', 'DashboardController@manage');

//PROFILE ROUTES
Route::get('/profile/{profileOwner}', 'ProfileController@show');
Route::get('/stargazer', 'ProfileController@search');

//PHP PARSE ROUTES
Route::post('/friendSystem','ParseController@friend')->name('friend');
Route::post('/blockSystem','ParseController@block')->name('block');
Route::post('/searchSystem','ParseController@search')->name('search');
Route::post('/messageSystem','ParseController@message')->name('message');
Route::post('/categories','ParseController@cats')->name('category');

Route::get('/categories','ParseController@categories');
Route::get('/passwordSystem','ParseController@password');

Route::post('/projectSystem','ParseController@project');
Route::post('/photoSystem/{User}','ParseController@photoHandle');

//IMAGE PULLING
Route::get('images/{filename}', function ($filename)
{
    $path = storage_path().'/app/public/images'.'/'.$filename;

    if(!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('uploads/user/{user}/{type}/{filename}', function ($user,$type,$filename)
{
    $path = storage_path().'/app/public/uploads/user'.'/'.$user.'/'.$type.'/'.$filename;

    if(!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

//Error Display Routes
Route::get('message', function ()
{
    return view('message');
});

Route::get('popUp', function ()
{
    return view('examples.popup');
});

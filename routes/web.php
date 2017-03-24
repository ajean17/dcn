<?php

Route::get('/', function ()
{
    return view('/homeIndex');
});

//Auth::routes();//everything related to login/regisration/passreset/logout
Route::get('/register','RegistrationController@create');
Route::get('/activation','RegistrationController@activation');
//Route::post('/register','RegistrationController@store');

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

//PROFILE ROUTES
Route::get('/profile/{profileOwner}', 'ProfileController@show');
Route::get('/stargazer', 'ProfileController@search');

//PHP PARSE ROUTES
Route::get('/friendSystem','ParseController@friend');
Route::get('/blockSystem','ParseController@block');
Route::get('/searchSystem','ParseController@search');
Route::get('/messageSystem','ParseController@message');
Route::get('/passwordSystem','ParseController@password');
Route::get('/categories','ParseController@categories');
Route::post('/photoSystem/{User}','ParseController@photoHandle');

//IMAGE PULLING
Route::get('images/{filename}', function ($filename)
{
    $path = resource_path().'/images'.'/'.$filename;

    if(!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('uploads/user/{user}/{type}/{filename}', function ($user,$type,$filename)
{
    $path = resource_path().'/uploads'.'/user'.'/'.$user.'/'.$type.'/'.$filename;

    if(!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

/*Route::get('/stargazer',function()
{
  return view('/search.index');
}); //search page


Route::get('/messages','MessageController@show'); //messenger
Route::get('/update',function()
{
  return view('/dashboard.update-messages');
});
Route::get('/getM',function()
{
  return view('/dashboard.get-messages');
});
Route::get('/profile/{profileOwner}','ProfileController@show');

Route::get('/settings','ProfileController@settings'); //account settings
*/

//Error Display Routes
Route::get('message', function ()
{
    return view('message');
});

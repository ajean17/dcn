<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function ()
{
    return view('/homeIndex');
});

//Auth::routes();//everything related to login/regisration/passreset/logout
Route::get('/register','RegistrationController@create');
Route::post('/register','RegistrationController@store');

Route::get('/login','SessionsController@create');
Route::post('/login','SessionsController@store');
Route::get('/logout','SessionsController@destroy');

Route::get('/stargazer',function()
{
  return view('/search.index');
}); //search page

Route::get('/dashboard',function()
{
  return view('/dashboard.home');
});//user's dashboard

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



Route::get('/friendSystem',function()
{
  return view('/phpParser.friendSystem');
});
Route::get('/blockSystem',function()
{
  return view('/phpParser.blockSystem');
});

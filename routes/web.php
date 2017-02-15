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

//Route::get('/home', 'HomeController@index');//set this to the user's dashboard

Route::get('/profile/{profile}','ProfileController@show');

Route::get('/settings','ProfileController@settings'); //account settings

Route::get('/stargazer',function()
{
  return view('/search.index');
}); //search page

Route::get('/messages',function()
{
  return view('/messenger.index');
}); //messenger

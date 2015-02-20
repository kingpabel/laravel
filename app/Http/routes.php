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

Route::group(['middleware' => 'guest'], function(){
Route::get('/', function()
{
	return View::make('loginPage');
});
});

Route::controller('login','LoginController');

Route::group(['middleware' => 'auth'], function(){
Route::controller('user','UserController');
});

Route::group(['middleware' => 'auth.company'], function(){
Route::controller('company','CompanyController');
});


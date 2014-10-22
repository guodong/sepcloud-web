<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'IndexController@index');
Route::get('login', 'IndexController@login');
Route::get('instance.json', 'InstanceController@indexjson');
Route::get('instance/status.json', 'InstanceController@statusjson');
Route::get('instance/start', 'InstanceController@start');
Route::get('instance/shutdown', 'InstanceController@shutdown');
Route::post('instance', 'InstanceController@create');
Route::get('user.json', 'UserController@indexjson');

Route::post('client/login', 'UserController@login');
Route::get('client', "ClientController@index");
Route::get('client/myvms', "ClientController@myvms");

Route::post('admin/login', "IndexController@adminlogin");
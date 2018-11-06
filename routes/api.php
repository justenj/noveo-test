<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('users', ['as' => 'users.store', 'uses' => 'UserController@store']);

Route::name('groups.')->prefix('groups')->group(function() {
    Route::get('/', 'GroupController@index')->name('index');
    Route::post('/', 'GroupController@store')->name('store');
    Route::put('{group}', 'GroupController@update')->name('update');
});
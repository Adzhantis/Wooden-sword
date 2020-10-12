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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('adminpanel')->middleware('auth')->group(function () {
    Route::get('/groups', 'AdminPanel\GroupController@index')->name('groups');
    Route::get('/groups/reset-counters', 'AdminPanel\GroupController@resetCounters');
});


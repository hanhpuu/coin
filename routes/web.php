<?php

/*
|---------------------------------DataController@extractData-----------------------------------------
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

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/price/past', 'PriceController@fetchAndSaveDataInPast' );
Route::get('/price/reset', 'PriceController@reset' );
Route::get('/price/present', 'PriceController@fetchAndSaveAllDataInPresent' );
Route::get('/price/distinct/present', 'DistinctPriceController@fetchAndSaveDataInPresent' );



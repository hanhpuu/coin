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

Route::get('/price/past', 'PriceController@fetchAndSaveDataInPast');
Route::get('/latest', 'DistinctPairController@SaveLatestPrice');

Route::group(['middleware' => 'auth'], function() {
	Route::resource('coins', 'CoinController', ['except' => ['delete']]);

	Route::resource('sources', 'SourceController', ['except' => ['delete']]);

	Route::resource('currency_pairs', 'CurrencyPairController', ['except' => ['delete']]);

	Route::resource('distinct_pairs', 'DistinctPairController', ['except' => ['delete']]);
	
	Route::get('/potential_group', 'DistinctPairController@enterPotentialGroupID');
	Route::post('/potential_group', 'DistinctPairController@checkPotentialGroupID');
});



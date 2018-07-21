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

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['middleware' => 'auth'], function() {
	Route::resource('coins', 'CoinController', ['except' => ['delete']]);

	Route::resource('sources', 'SourceController', ['except' => ['delete']]);

	Route::resource('currency_pairs', 'CurrencyPairController', ['except' => ['delete']]);

	Route::resource('distinct_pairs', 'DistinctPairController', ['except' => ['delete']]);

	Route::get('/potential_group', 'DistinctPairController@enterPotentialGroupID');
	Route::post('/potential_group', 'DistinctPairController@checkPotentialGroupID');

	Route::get('/time', 'CurrencyPairController@enterTime');
	Route::post('/time', 'CurrencyPairController@fetchDataDuringTime');
});



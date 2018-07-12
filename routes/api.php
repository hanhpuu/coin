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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/register', 'UserController@register');
Route::post('auth/login', 'UserController@login');
Route::post('auth/logout','UserController@logout');

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('user-info', 'UserController@getUserInfo');
	Route::post('test','UserController@test');
	
	Route::post('source/add','SourceController@addSourceName');
	Route::post('coin/add','CoinController@addCoinName');
	Route::post('pair/add','CurrencyPairController@addPairName');
	
	Route::post('coin/distinct/add','DistinctPairController@addDistinctPairs');
	
	Route::get('price/fluctuation','CurrencyPairController@checkPriceFluctuation');
});


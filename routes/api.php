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

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('user-info', 'UserController@getUserInfo');
	Route::post('auth/logout','UserController@logout');
	Route::post('test','UserController@test');
});

Route::post('add-source','SourceController@addSourceName');
Route::post('add-coin','CoinController@addCoinName');
Route::post('add-pair','CurrencyPairController@addPairName');
Route::post('check-price-fluctuation','CurrencyPairController@checkPriceFluctuation');
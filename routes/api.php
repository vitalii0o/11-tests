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


Route::get('lots', 'LotController@all');
Route::get('lots/{id}', 'LotController@get');

// TODO:REGISTERED USERS
Route::middleware('auth:api')->post('lots', 'LotController@post');
Route::middleware('auth:api')->post('trades', 'TradeController@post');

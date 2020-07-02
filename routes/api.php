<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/fields', 'EntityController@getFields')->name('getFieldsAPI');
Route::get('/entity', 'EntityController@getEntity')->name('getEntityAPI');
Route::post('/entity', 'EntityController@createEntity')->name('createEntityAPI');
Route::post('/updateEntity', 'EntityController@deleteEntity')->name('updateEntityAPI');
Route::post('/deleteEntity', 'EntityController@deleteEntity')->name('deleteEntityAPI');

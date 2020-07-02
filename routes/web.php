<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/get_fields', function () {
    return view('zoho/get_fields');
})->name('getFields');

Route::get('/getEntity', function () {
    return view('zoho/get_entity');
})->name('getEntity');

Route::get('/createEntity', function () {
    return view('zoho/create_entity');
})->name('createEntity');

Route::post('/createEntityForm', 'EntityController@createEntityForm')->name('createEntityForm');

Route::get('/updateEntity', function () {
    return view('zoho/update_entity');
})->name('updateEntity');

Route::post('/updateEntityForm', 'EntityController@updateEntityForm')->name('updateEntityForm');

Route::get('/deleteEntity', function () {
    return view('zoho/delete_entity');
})->name('deleteEntity');

<?php

use Illuminate\Support\Facades\Route;

Route::post('/package/add', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@add'
]);

Route::post('/package/add/confirm', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@confirmAdd'
]);

Route::put('/package/update', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@update'
]);

Route::delete('/package/delete', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@delete'
]);

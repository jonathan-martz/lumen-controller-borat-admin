<?php

use Illuminate\Support\Facades\Route;

Route::put('/package/edit', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@edit'
]);

Route::delete('/package/delete', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@delete'
]);

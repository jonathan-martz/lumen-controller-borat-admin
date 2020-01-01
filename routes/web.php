<?php

use Illuminate\Support\Facades\Route;

Route::put('/borat/package/edit', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@edit'
]);

Route::delete('/borat/package/delete', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@delete'
]);

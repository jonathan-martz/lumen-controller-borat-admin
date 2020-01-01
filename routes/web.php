<?php

use Illuminate\Support\Facades\Route;

Route::put('/repo/edit', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@edit'
]);

Route::delete('/repo/delete', [
    'middleware' => ['auth', 'xss', 'https'],
    'uses' => 'App\Http\Controllers\BoratAdminController@delete'
]);

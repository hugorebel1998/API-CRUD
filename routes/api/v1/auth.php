<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {

    Route::post('/registro', [
        'uses' => 'AuthController@register'
    ]);

    Route::get('/login', [
        'uses' => 'AuthController@login'
    ]);

    Route::post('/logout', [
        'middleware' => 'auth:api',
        'uses' => 'AuthController@logout'
    ]);
});

<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'usuarios', 'namespace' => 'Usuarios'], function () {

    Route::get('/', [
        'middleware' => 'auth:api',
        'uses' => 'UserController@listar'
    ]);

    Route::get('/{usuario}/mostrar', [
        'middleware' => 'auth:api',
        'uses' => 'UserController@listar'
    ]);

    Route::post('/crear', [
        'middleware' => 'auth:api',
        'uses' => 'UserController@crear'
    ]);

    Route::put('/update/{usuario}', [
        'middleware' => 'auth:api',
        'uses' => 'UserController@update'
    ]);

    Route::delete('/delete/{usuario}', [
        'middleware' => 'auth:api',
        'uses' => 'UserController@delete'
    ]);

    Route::get('/restablecer/{usuario}', [
        'middleware' => 'auth:api',
        'uses' => 'UserController@restablecer'
    ]);

    Route::get('/reporte', [
        'middleware' => 'auth:api',
        'uses' => 'UserController@reporte'
    ]);
});

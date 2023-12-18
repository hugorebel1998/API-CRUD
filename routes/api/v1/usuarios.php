<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'usuarios', 'namespace' => 'Usuarios'], function () {

    Route::get('/', [
        'uses' => 'UserController@listar'
    ]);

    // Route::get('/{usuario}', [
    //     'uses' => 'UserController@listar'
    // ]);

    Route::post('/crear', [
        'uses' => 'UserController@crear'
    ]);

    Route::put('/update/{usuario}', [
        'uses' => 'UserController@update'
    ]);

    Route::delete('/delete/{usuario}', [
        'uses' => 'UserController@delete'
    ]);

    Route::get('/restablecer/{usuario}', [
        'uses' => 'UserController@restablecer'
    ]);

    Route::get('/reporte', [
        'uses' => 'UserController@reporte'
    ]);
});

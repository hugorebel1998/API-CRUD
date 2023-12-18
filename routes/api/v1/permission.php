<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'permisos', 'namespace' => 'Permission'], function () {

    Route::get('/', [
        'middleware' => 'auth:api',
        'uses' => 'PermissionController@listar'
    ]);

    Route::get('/{permiso}', [
        'middleware' => 'auth:api',
        'uses' => 'PermissionController@listar'
    ]);

    Route::post('/crear', [
        'middleware' => 'auth:api',
        'uses' => 'PermissionController@crear'
    ]);

    Route::put('/update/{permiso}', [
        'middleware' => 'auth:api',
        'uses' => 'PermissionController@update'
    ]);
});

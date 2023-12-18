<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'permisos', 'namespace' => 'Permission'], function () {

    Route::get('/', [
        'uses' => 'PermissionController@listar'
    ]);

    Route::get('/{permiso}', [
        'uses' => 'PermissionController@listar'
    ]);

    Route::post('/crear', [
        'uses' => 'PermissionController@crear'
    ]);

    Route::put('/update/{permiso}', [
        'uses' => 'PermissionController@update'
    ]);
});

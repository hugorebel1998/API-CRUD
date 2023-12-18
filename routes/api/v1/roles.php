<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'roles', 'namespace' => 'Roles'], function () {

    Route::get('/', [
        'middleware' => 'auth:api',
        'uses' => 'RoleController@listar'
    ]);

    Route::get('/{role}', [
        'middleware' => 'auth:api',
        'uses' => 'RoleController@listar'
    ]);

    Route::post('/crear', [
        'middleware' => 'auth:api',
        'uses' => 'RoleController@crear'
    ]);

    Route::put('/update/{role}', [
        'middleware' => 'auth:api',
        'uses' => 'RoleController@update'
    ]);

    Route::post('/asignar-permisos/{role}', [
        'middleware' => 'auth:api',
        'uses' => 'RoleController@asignarPermiso'
    ]);
});

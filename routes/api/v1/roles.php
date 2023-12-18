<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'roles', 'namespace' => 'Roles'], function () {

    Route::get('/', [
        'uses' => 'RoleController@listar'
    ]);

    Route::get('/{role}', [
        'uses' => 'RoleController@listar'
    ]);

    Route::post('/crear', [
        'uses' => 'RoleController@crear'
    ]);

    Route::put('/update/{role}', [
        'uses' => 'RoleController@update'
    ]);
});

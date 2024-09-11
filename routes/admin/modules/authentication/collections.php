<?php
/**
 * No Authentication
 */
$router->group(['prefix' => 'admin'], function () use ($router) {
    Route::post('login', 'Admin\Modules\Authentication\AuthController@login');
});


/**
 * Have Authentication
 */
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    Route::post('logout', 'Admin\Modules\Authentication\AuthController@logout');
    Route::post('getUser', 'Admin\Modules\Authentication\AuthController@getUser');
});

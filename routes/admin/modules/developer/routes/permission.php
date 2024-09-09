<?php
// User
Route::post('permission/authorize', 'Admin\Modules\Developer\PermissionController@checkAuthorize');

/**
 * Get permission data from database
 */
Route::post('permission/getPermission', 'Admin\Modules\Developer\PermissionController@getPermission');
Route::post('permission/get', 'Admin\Modules\Developer\PermissionController@get');
Route::post('permission/lists', 'Admin\Modules\Developer\PermissionController@lists');
Route::post('permission/store', 'Admin\Modules\Developer\PermissionController@store');
Route::post('permission/delete', 'Admin\Modules\Developer\PermissionController@delete');
Route::post('permission/update', 'Admin\Modules\Developer\PermissionController@update');

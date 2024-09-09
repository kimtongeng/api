<?php

Route::post('role/get', 'Admin\Modules\Setting\User\RoleController@get');
Route::post('role/store', 'Admin\Modules\Setting\User\RoleController@store');
Route::post('role/update', 'Admin\Modules\Setting\User\RoleController@update');
Route::post('role/delete', 'Admin\Modules\Setting\User\RoleController@delete');
Route::post('role/update_status', 'Admin\Modules\Setting\User\RoleController@updateStatus');
Route::post('role/get_module_permission', 'Admin\Modules\Setting\User\RoleController@getModulePermission');
Route::post('role/get_edit_module', 'Admin\Modules\Setting\User\RoleController@getEditModule');
Route::post('role/get_by_user_type', 'Admin\Modules\Setting\User\RoleController@getByUserType');
Route::post('role/get_update', 'Admin\Modules\Setting\User\RoleController@getUpdate');

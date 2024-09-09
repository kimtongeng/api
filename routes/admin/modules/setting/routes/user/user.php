<?php
Route::post('user/get', 'Admin\Modules\Setting\User\UserController@get');
Route::post('user/store', 'Admin\Modules\Setting\User\UserController@store');
Route::post('user/delete', 'Admin\Modules\Setting\User\UserController@update');
Route::post('user/update', 'Admin\Modules\Setting\User\UserController@update');
Route::post('user/update_status', 'Admin\Modules\Setting\User\UserController@updateStatus');
Route::post('user/update_fcm_token', 'Admin\Modules\Setting\User\UserController@updateFCMToken');

<?php

Route::post('module/get', 'Admin\Modules\Developer\ModuleController@get');
Route::post('module/store', 'Admin\Modules\Developer\ModuleController@store');
Route::post('module/update', 'Admin\Modules\Developer\ModuleController@update');
Route::post('module/delete', 'Admin\Modules\Developer\ModuleController@delete');

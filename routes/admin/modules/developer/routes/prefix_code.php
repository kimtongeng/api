<?php

//Tag
Route::post('prefix_code/get', 'Admin\Modules\Developer\PrefixCodeController@get');
Route::post('prefix_code/store', 'Admin\Modules\Developer\PrefixCodeController@store');
Route::post('prefix_code/update', 'Admin\Modules\Developer\PrefixCodeController@update');
Route::post('prefix_code/get_combo_list', 'Admin\Modules\Developer\PrefixCodeController@getComboList');
Route::post('prefix_code/update_status', 'Admin\Modules\Developer\PrefixCodeController@updateStatus');

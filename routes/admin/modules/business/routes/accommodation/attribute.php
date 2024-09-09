<?php
Route::post('attribute/store', 'Admin\Modules\Business\Accommodation\AttributeListController@store');
Route::post('attribute/get', 'Admin\Modules\Business\Accommodation\AttributeListController@get');
Route::post('attribute/update', 'Admin\Modules\Business\Accommodation\AttributeListController@update');
Route::post('attribute/delete', 'Admin\Modules\Business\Accommodation\AttributeListController@delete');
Route::post('attribute/change_status', 'Admin\Modules\Business\Accommodation\AttributeListController@changeStatus');
Route::post('attribute/get_select_data', 'Admin\Modules\Business\Accommodation\AttributeListController@getSelectData');

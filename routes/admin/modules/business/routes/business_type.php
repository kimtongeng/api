<?php

Route::post('business_type/get', 'Admin\Modules\Business\BusinessTypeController@get');
Route::post('business_type/store', 'Admin\Modules\Business\BusinessTypeController@store');
Route::post('business_type/update', 'Admin\Modules\Business\BusinessTypeController@update');
Route::post('business_type/delete', 'Admin\Modules\Business\BusinessTypeController@delete');
Route::post('business_type/get_auto_order', 'Admin\Modules\Business\BusinessTypeController@getAutoOrder');
Route::post('business_type/change_status', 'Admin\Modules\Business\BusinessTypeController@changeStatusBusinessType');

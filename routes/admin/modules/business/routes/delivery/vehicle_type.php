<?php

Route::post('vehicle_type/get', 'Admin\Modules\Business\Delivery\VehicleTypeListController@get');
Route::post('vehicle_type/store', 'Admin\Modules\Business\Delivery\VehicleTypeListController@store');
Route::post('vehicle_type/update', 'Admin\Modules\Business\Delivery\VehicleTypeListController@update');
Route::post('vehicle_type/delete', 'Admin\Modules\Business\Delivery\VehicleTypeListController@delete');
Route::post('vehicle_type/change_status', 'Admin\Modules\Business\Delivery\VehicleTypeListController@changeStatus');
Route::post('vehicle_type/get_auto_order', 'Admin\Modules\Business\Delivery\VehicleTypeListController@getAutoOrder');

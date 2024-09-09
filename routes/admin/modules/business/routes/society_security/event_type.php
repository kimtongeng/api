<?php

Route::post('event_type/get','Admin\Modules\Business\SocietySecurity\EventTypeListController@get');
Route::post('event_type/store','Admin\Modules\Business\SocietySecurity\EventTypeListController@store');
Route::post('event_type/update','Admin\Modules\Business\SocietySecurity\EventTypeListController@update');
Route::post('event_type/delete', 'Admin\Modules\Business\SocietySecurity\EventTypeListController@delete');
Route::post('event_type/change_status', 'Admin\Modules\Business\SocietySecurity\EventTypeListController@changeStatus');
Route::post('event_type/get_auto_order', 'Admin\Modules\Business\SocietySecurity\EventTypeListController@getAutoOrder');

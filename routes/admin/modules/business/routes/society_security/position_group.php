<?php

Route::post('position_group/get', 'Admin\Modules\Business\SocietySecurity\PositionGroupListController@get');
Route::post('position_group/store', 'Admin\Modules\Business\SocietySecurity\PositionGroupListController@store');
Route::post('position_group/update', 'Admin\Modules\Business\SocietySecurity\PositionGroupListController@update');
Route::post('position_group/delete', 'Admin\Modules\Business\SocietySecurity\PositionGroupListController@delete');
Route::post('position_group/change_status', 'Admin\Modules\Business\SocietySecurity\PositionGroupListController@changeStatus');

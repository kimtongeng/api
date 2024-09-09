<?php

Route::post('add_room', 'Mobile\Modules\Accommodation\Profile\RoomCrudAPIController@addRoom');
Route::post('edit_room', 'Mobile\Modules\Accommodation\Profile\RoomCrudAPIController@editRoom');
Route::post('delete_room', 'Mobile\Modules\Accommodation\Profile\RoomCrudAPIController@deleteRoom');
Route::post('get_my_room', 'Mobile\Modules\Accommodation\Profile\RoomCrudAPIController@getMyRoom');
Route::post('change_status', 'Mobile\Modules\Accommodation\Profile\RoomCrudAPIController@changeStatus');

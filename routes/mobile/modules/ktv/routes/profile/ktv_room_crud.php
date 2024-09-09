<?php

Route::post('add_ktv_room', 'Mobile\Modules\KTV\Profile\KTVRoomCrudAPIController@addKTVRoom');
Route::post('edit_ktv_room', 'Mobile\Modules\KTV\Profile\KTVRoomCrudAPIController@editKTVRoom');
Route::post('delete_ktv_room', 'Mobile\Modules\KTV\Profile\KTVRoomCrudAPIController@deleteKTVRoom');
Route::post('get_my_ktv_room', 'Mobile\Modules\KTV\Profile\KTVRoomCrudAPIController@getMyKTVRoom');
Route::post('change_status_room', 'Mobile\Modules\KTV\Profile\KTVRoomCrudAPIController@changeStatus');

<?php

Route::post('add_room_type', 'Mobile\Modules\Accommodation\Profile\RoomTypeCrudAPIController@addRoomType');
Route::post('edit_room_type', 'Mobile\Modules\Accommodation\Profile\RoomTypeCrudAPIController@editRoomType');
Route::post('delete_room_type', 'Mobile\Modules\Accommodation\Profile\RoomTypeCrudAPIController@deleteRoomType');
Route::post('get_room_type_list', 'Mobile\Modules\Accommodation\Profile\RoomTypeCrudAPIController@getRoomTypeList');

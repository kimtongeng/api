<?php

Route::post('accommodation_list/get', 'Admin\Modules\Business\Accommodation\AccommodationListController@get');
Route::post('accommodation_list/get_detail', 'Admin\Modules\Business\Accommodation\AccommodationListController@getDetail');
Route::post('accommodation_list/get_select_data', 'Admin\Modules\Business\Accommodation\AccommodationListController@getSelectData');
Route::post('room_list/get_room_list', 'Admin\Modules\Business\Accommodation\AccommodationListController@getRoomList');
Route::post('accommodation_list/update_verify', 'Admin\Modules\Business\Accommodation\AccommodationListController@updateVerify');


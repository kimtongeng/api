<?php

Route::post('ktv_list/get', 'Admin\Modules\Business\KTV\KTVListController@get');
Route::post('ktv_list/get_detail', 'Admin\Modules\Business\KTV\KTVListController@getDetail');
Route::post('ktv_list/change_active', 'Admin\Modules\Business\KTV\KTVListController@changeActive');
Route::post('ktv_list/update_verify', 'Admin\Modules\Business\KTV\KTVListController@updateVerify');
Route::post('ktv_list/get_select_data', 'Admin\Modules\Business\KTV\KTVListController@getSelectData');
Route::post('ktv_list/get_select_ktv_detail', 'Admin\Modules\Business\KTV\KTVListController@getSelectKTVDetail');
Route::post('food_list/get_food_list', 'Admin\Modules\Business\KTV\KTVListController@getFoodList');
Route::post('ktv_girl_list/get_ktv_girl_list', 'Admin\Modules\Business\KTV\KTVListController@getKTVGirlList');
Route::post('room_ktv_list/get_room_ktv_list', 'Admin\Modules\Business\KTV\KTVListController@getRoomKTVList');

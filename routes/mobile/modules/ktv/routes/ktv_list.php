<?php

Route::post('get_ktv_filter_sort', 'Mobile\Modules\KTV\KTVListAPIController@getKTVFilterSort');
Route::post('get_ktv_detail', 'Mobile\Modules\KTV\KTVListAPIController@getKTVDetail');
Route::post('update_view_ktv', 'Mobile\Modules\KTV\KTVListAPIController@updateViewKTV');
Route::post('get_ktv_detail_nearby_place', 'Mobile\Modules\KTV\KTVListAPIController@getKTVDetailNearbyPlace');

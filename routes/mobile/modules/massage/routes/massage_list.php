<?php

Route::post('get_massage_filter_sort', 'Mobile\Modules\Massage\MassageListAPIController@getMassageFilterSort');
Route::post('get_massage_detail', 'Mobile\Modules\Massage\MassageListAPIController@getMassageDetail');
Route::post('update_view_massage', 'Mobile\Modules\Massage\MassageListAPIController@updateViewMassage');
Route::post('get_massage_detail_nearby_place', 'Mobile\Modules\Massage\MassageListAPIController@getMassageDetailNearbyPlace');

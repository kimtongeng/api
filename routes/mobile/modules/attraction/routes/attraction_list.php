<?php

Route::post('get_attraction_filter_sort', 'Mobile\Modules\Attraction\AttractionListAPIController@getAttractionFilterSort');
Route::post('get_attraction_detail', 'Mobile\Modules\Attraction\AttractionListAPIController@getAttractionDetail');
Route::post('get_attraction_detail_place_nearby', 'Mobile\Modules\Attraction\AttractionListAPIController@getAttractionDetailPlaceNearby');
Route::post('update_view_attraction', 'Mobile\Modules\Attraction\AttractionListAPIController@updateViewAttraction');

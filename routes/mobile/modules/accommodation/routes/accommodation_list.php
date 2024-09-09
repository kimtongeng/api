<?php

Route::post('get_accommodation_filter_sort', 'Mobile\Modules\Accommodation\AccommodationListAPIController@getAccommodationFilterSort');
Route::post('get_accommodation_detail', 'Mobile\Modules\Accommodation\AccommodationListAPIController@getAccommodationDetail');
Route::post( 'update_view_accommodation', 'Mobile\Modules\Accommodation\AccommodationListAPIController@updateViewAccommodation');
Route::post('get_accommodation_detail_nearby_place', 'Mobile\Modules\Accommodation\AccommodationListAPIController@getAccommodationDetailNearbyPlace');

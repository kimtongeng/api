<?php

Route::post('get_property_filter_sort', 'Mobile\Modules\RealEstate\PropertyListAPIController@getPropertyFilterSort');
Route::post('get_property_detail', 'Mobile\Modules\RealEstate\PropertyListAPIController@getPropertyDetail');
Route::post('update_view_property', 'Mobile\Modules\RealEstate\PropertyListAPIController@updateViewProperty');
Route::post('get_property_detail_nearby_place', 'Mobile\Modules\RealEstate\PropertyListAPIController@getPropertyDetailNearbyPlace');

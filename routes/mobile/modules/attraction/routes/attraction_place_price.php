<?php

Route::post('add_place_price', 'Mobile\Modules\Attraction\AttractionPlacePriceCrudAPIController@addPlacePrice');
Route::post('edit_place_price', 'Mobile\Modules\Attraction\AttractionPlacePriceCrudAPIController@editPlacePrice');
Route::post('delete_place_price', 'Mobile\Modules\Attraction\AttractionPlacePriceCrudAPIController@deletePlacePrice');
Route::post('get_place_price_list', 'Mobile\Modules\Attraction\AttractionPlacePriceCrudAPIController@getPlacePriceList');

<?php

Route::post('add_attraction_category','Mobile\Modules\Attraction\AttractionCategoryCrudAPIController@addAttractionCategory');
Route::post('edit_attraction_category', 'Mobile\Modules\Attraction\AttractionCategoryCrudAPIController@editAttractionCategory');
Route::post('delete_attraction_category', 'Mobile\Modules\Attraction\AttractionCategoryCrudAPIController@deleteAttractionCategory');
Route::post('get_my_attraction_category', 'Mobile\Modules\Attraction\AttractionCategoryCrudAPIController@getMyAttractionCategory');

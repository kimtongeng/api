<?php

Route::post('add_property', 'Mobile\Modules\RealEstate\PropertyCrudAPIController@addProperty');
Route::post('edit_property', 'Mobile\Modules\RealEstate\PropertyCrudAPIController@editProperty');
Route::post('delete_property', 'Mobile\Modules\RealEstate\PropertyCrudAPIController@deleteProperty');
Route::post('get_my_property', 'Mobile\Modules\RealEstate\PropertyCrudAPIController@getMyProperty');

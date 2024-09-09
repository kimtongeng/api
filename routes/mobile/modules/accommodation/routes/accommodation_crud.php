<?php

Route::post('get_accommodation_category_list', 'Mobile\Modules\Accommodation\AccommodationCrudAPIController@getAccommodationCategoryList');
Route::post('add_accommodation', 'Mobile\Modules\Accommodation\AccommodationCrudAPIController@addAccommodation');
Route::post('edit_accommodation', 'Mobile\Modules\Accommodation\AccommodationCrudAPIController@editAccommodation');
Route::post('delete_accommodation', 'Mobile\Modules\Accommodation\AccommodationCrudAPIController@deleteAccommodation');
Route::post('get_my_accommodation', 'Mobile\Modules\Accommodation\AccommodationCrudAPIController@getMyAccommodation');

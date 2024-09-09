<?php
Route::post('accommodation_category/get', 'Admin\Modules\Business\Accommodation\AccommodationCategoryListController@get');
Route::post('accommodation_category/store', 'Admin\Modules\Business\Accommodation\AccommodationCategoryListController@store');
Route::post('accommodation_category/update', 'Admin\Modules\Business\Accommodation\AccommodationCategoryListController@update');
Route::post('accommodation_category/delete', 'Admin\Modules\Business\Accommodation\AccommodationCategoryListController@delete');
Route::post('accommodation_category/change_status', 'Admin\Modules\Business\Accommodation\AccommodationCategoryListController@changeStatus');
Route::post('accommodation_category/get_auto_order', 'Admin\Modules\Business\Accommodation\AccommodationCategoryListController@getAutoOrder');

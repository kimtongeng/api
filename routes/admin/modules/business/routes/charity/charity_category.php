<?php 

Route::post('charity_category/get', 'Admin\Modules\Business\Charity\CharityCategoryListController@get');
Route::post('charity_category/store', 'Admin\Modules\Business\Charity\CharityCategoryListController@store');
Route::post('charity_category/update', 'Admin\Modules\Business\Charity\CharityCategoryListController@update');
Route::post('charity_category/delete', 'Admin\Modules\Business\Charity\CharityCategoryListController@delete');
Route::post('charity_category/change_status', 'Admin\Modules\Business\Charity\CharityCategoryListController@changeStatus');
Route::post('charity_category/get_auto_order', 'Admin\Modules\Business\Charity\CharityCategoryListController@getAutoOrder');
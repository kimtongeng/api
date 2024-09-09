<?php
Route::post('banner/get', 'Admin\Modules\Banner\BannerController@get');
Route::post('banner/store', 'Admin\Modules\Banner\BannerController@store');
Route::post('banner/delete', 'Admin\Modules\Banner\BannerController@delete');
Route::post('banner/update', 'Admin\Modules\Banner\BannerController@update');
Route::post('banner/get_select_data', 'Admin\Modules\Banner\BannerController@getSelectData');
Route::post('banner/change_status', 'Admin\Modules\Banner\BannerController@changeStatus');
Route::post('banner/upload_media', 'Admin\Modules\Banner\BannerController@uploadMedia');
Route::post('banner/get_category_in_shop', 'Admin\Modules\Banner\BannerController@getCategoryInShop');
Route::post('banner/get_sub_category', 'Admin\Modules\Banner\BannerController@getSubCategory');

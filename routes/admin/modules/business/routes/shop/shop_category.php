<?php
Route::post('shop_category/get', 'Admin\Modules\Business\Shop\ShopCategoryListController@get');
Route::post('shop_category/store', 'Admin\Modules\Business\Shop\ShopCategoryListController@store');
Route::post('shop_category/update', 'Admin\Modules\Business\Shop\ShopCategoryListController@update');
Route::post('shop_category/delete', 'Admin\Modules\Business\Shop\ShopCategoryListController@delete');
Route::post( 'shop_category/change_status', 'Admin\Modules\Business\Shop\ShopCategoryListController@changeStatus');
Route::post('shop_category/get_auto_order', 'Admin\Modules\Business\Shop\ShopCategoryListController@getAutoOrder');
Route::post('shop_category/get_app_country_list', 'Admin\Modules\Business\Shop\ShopCategoryListController@getAppCountryList');

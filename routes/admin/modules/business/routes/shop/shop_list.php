<?php

Route::post('shop_list/get', 'Admin\Modules\Business\Shop\ShopListController@get');
Route::post('shop_list/change_active', 'Admin\Modules\Business\Shop\ShopListController@changeActive');
Route::post('shop_list/update_verify', 'Admin\Modules\Business\Shop\ShopListController@updateVerify');
Route::post('shop_list/get_select_data', 'Admin\Modules\Business\Shop\ShopListController@getSelectData');
Route::post('shop_list/get_detail', 'Admin\Modules\Business\Shop\ShopListController@getDetail');
Route::post('product_list/get_product_list', 'Admin\Modules\Business\Shop\ShopListController@getProductList');
Route::post('shop_list/get_select_shop_detail', 'Admin\Modules\Business\Shop\ShopListController@getSelectShopDetail');
Route::post('product_list/change_status_suspend', 'Admin\Modules\Business\Shop\ShopListController@changeStatusSuspend');

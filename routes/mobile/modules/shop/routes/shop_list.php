<?php

Route::post('get_shop_filter_sort', 'Mobile\Modules\Shop\ShopListAPIController@getShopFilterSort');
Route::post('get_shop_detail', 'Mobile\Modules\Shop\ShopListAPIController@getShopDetail');
Route::post('update_view_shop', 'Mobile\Modules\Shop\ShopListAPIController@updateViewShop');
Route::post('get_shop_filter_transaction_list', 'Mobile\Modules\Shop\ShopListAPIController@getShopFilterTransactionList');



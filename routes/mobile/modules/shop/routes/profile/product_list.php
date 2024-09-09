<?php

Route::post('get_product_filter_sort', 'Mobile\Modules\Shop\Profile\ProductListAPIController@getProductFilterSort');
Route::post('get_product_detail', 'Mobile\Modules\Shop\Profile\ProductListAPIController@getProductDetail');
Route::post('get_product_stock_report', 'Mobile\Modules\Shop\Profile\ProductListAPIController@getProductStockReport');

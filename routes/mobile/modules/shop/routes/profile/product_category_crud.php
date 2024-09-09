<?php

Route::post('add_product_category', 'Mobile\Modules\Shop\Profile\ProductCategoryCrudAPIController@addProductCategory');
Route::post('edit_product_category', 'Mobile\Modules\Shop\Profile\ProductCategoryCrudAPIController@editProductCategory');
Route::post('delete_product_category', 'Mobile\Modules\Shop\Profile\ProductCategoryCrudAPIController@deleteProductCategory');
Route::post('get_my_product_category', 'Mobile\Modules\Shop\Profile\ProductCategoryCrudAPIController@getMyProductCategory');

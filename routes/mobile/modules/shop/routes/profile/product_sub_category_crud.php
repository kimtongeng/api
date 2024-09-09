<?php

Route::post('add_product_sub_category', 'Mobile\Modules\Shop\Profile\ProductSubCategoryCrudAPIController@addProductSubCategory');
Route::post('edit_product_sub_category', 'Mobile\Modules\Shop\Profile\ProductSubCategoryCrudAPIController@editProductSubCategory');
Route::post('delete_product_sub_category', 'Mobile\Modules\Shop\Profile\ProductSubCategoryCrudAPIController@deleteProductSubCategory');
Route::post('get_my_product_sub_category', 'Mobile\Modules\Shop\Profile\ProductSubCategoryCrudAPIController@getMyProductSubCategory');

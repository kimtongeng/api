<?php

Route::post('add_product_brand', 'Mobile\Modules\Shop\Profile\ProductBrandCrudAPIController@addProductBrand');
Route::post('edit_product_brand', 'Mobile\Modules\Shop\Profile\ProductBrandCrudAPIController@editProductBrand');
Route::post('delete_product_brand', 'Mobile\Modules\Shop\Profile\ProductBrandCrudAPIController@deleteProductBrand');
Route::post('get_my_product_brand', 'Mobile\Modules\Shop\Profile\ProductBrandCrudAPIController@getMyProductBrand');

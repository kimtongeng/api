<?php

Route::post('add_product_model', 'Mobile\Modules\Shop\Profile\ProductModelCrudAPIController@addProductModel');
Route::post('edit_product_model', 'Mobile\Modules\Shop\Profile\ProductModelCrudAPIController@editProductModel');
Route::post('delete_product_model', 'Mobile\Modules\Shop\Profile\ProductModelCrudAPIController@deleteProductModel');
Route::post('get_my_product_model', 'Mobile\Modules\Shop\Profile\ProductModelCrudAPIController@getMyProductModel');

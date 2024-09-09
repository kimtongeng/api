<?php

Route::post('add_product', 'Mobile\Modules\Shop\Profile\ProductCrudAPIController@addProduct');
Route::post('edit_product', 'Mobile\Modules\Shop\Profile\ProductCrudAPIController@editProduct');
Route::post('delete_product', 'Mobile\Modules\Shop\Profile\ProductCrudAPIController@deleteProduct');
Route::post('get_my_product', 'Mobile\Modules\Shop\Profile\ProductCrudAPIController@getMyProduct');
Route::post('update_product_qty', 'Mobile\Modules\Shop\Profile\ProductCrudAPIController@updateProductQty');


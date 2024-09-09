<?php

Route::post('add_shop', 'Mobile\Modules\Shop\ShopCrudAPIController@addShop');
Route::post('edit_shop', 'Mobile\Modules\Shop\ShopCrudAPIController@editShop');
Route::post('delete_shop', 'Mobile\Modules\Shop\ShopCrudAPIController@deleteShop');
Route::post('get_my_shop', 'Mobile\Modules\Shop\ShopCrudAPIController@getMyShop');

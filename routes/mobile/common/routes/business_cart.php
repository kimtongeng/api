<?php
Route::post('add_item_to_cart', 'Mobile\Common\BusinessCardAPIController@addItemToCart');
Route::post('adjust_item_quantity', 'Mobile\Common\BusinessCardAPIController@adjustItemQuantity');
Route::post('remove_item_from_cart', 'Mobile\Common\BusinessCardAPIController@removeItemFromCart');
Route::post('get_cart_list', 'Mobile\Common\BusinessCardAPIController@getCartList');

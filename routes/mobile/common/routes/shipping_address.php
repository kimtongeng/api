<?php
Route::post('add_shipping_address', 'Mobile\Common\ShippingAddressAPIController@addShippingAddress');
Route::post('get_shipping_address', 'Mobile\Common\ShippingAddressAPIController@getShippingAddress');
Route::post('update_shipping_address', 'Mobile\Common\ShippingAddressAPIController@updateShippingAddress');
Route::post('remove_shipping_address', 'Mobile\Common\ShippingAddressAPIController@removeShippingAddress');
Route::post('change_default_shipping_address', 'Mobile\Common\ShippingAddressAPIController@changeDefaultShippingAddress');

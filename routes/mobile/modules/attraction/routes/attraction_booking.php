<?php

Route::post('booking_attraction', 'Mobile\Modules\Attraction\AttractionBookingAPIController@bookingAttractionPlace');
Route::post('get_sale_list_attraction', 'Mobile\Modules\Attraction\AttractionBookingAPIController@getSaleListAttraction');
Route::post('get_sale_list_attraction_detail', 'Mobile\Modules\Attraction\AttractionBookingAPIController@getSaleListAttractionDetail');
Route::post('change_status_sale_attraction', 'Mobile\Modules\Attraction\AttractionBookingAPIController@changeStatusSaleAttraction');
Route::post('add_payment_attraction', 'Mobile\Modules\Attraction\AttractionBookingAPIController@addPaymentAttraction');

<?php

Route::post('check_massager_filter_time', 'Mobile\Modules\Massage\MassageBookingAPIController@checkMassagerFilterTime');
Route::post('booking_massage_service', 'Mobile\Modules\Massage\MassageBookingAPIController@bookingMassageService');
Route::post('get_sale_list_massage', 'Mobile\Modules\Massage\MassageBookingAPIController@getSaleListMassage');
Route::post('get_sale_list_massage_detail', 'Mobile\Modules\Massage\MassageBookingAPIController@getSaleListMassageDetail');
Route::post('change_status_sale_list_massage', 'Mobile\Modules\Massage\MassageBookingAPIController@changeStatusSaleListMassage');
Route::post('add_payment_massage', 'Mobile\Modules\Massage\MassageBookingAPIController@addPaymentMassageShop');

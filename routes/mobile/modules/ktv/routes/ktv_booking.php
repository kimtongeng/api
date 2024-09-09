<?php

Route::post('check_ktv_room_filter_date', 'Mobile\Modules\KTV\KTVBookingAPIController@checkKTVRoomFilterDate');
Route::post('check_ktv_girl_filter_date', 'Mobile\Modules\KTV\KTVBookingAPIController@checkKTVGirlFilter');
Route::post('ktv_booking', 'Mobile\Modules\KTV\KTVBookingAPIController@bookingKTV');
Route::post('change_status_sale_ktv', 'Mobile\Modules\KTV\KTVBookingAPIController@changeStatusSaleKTV');
Route::post('add_payment_ktv_shop', 'Mobile\Modules\KTV\KTVBookingAPIController@addPaymentKTVShop');
Route::post('get_sale_list_ktv', 'Mobile\Modules\KTV\KTVBookingAPIController@getSaleListKTV');
Route::post('get_sale_ktv_detail', 'Mobile\Modules\KTV\KTVBookingAPIController@getSaleKTVDetail');
Route::post('change_active_ktv_customer_sale_list', 'Mobile\Modules\KTV\KTVBookingAPIController@changeActiveKTVCustomerSaleList');

<?php

Route::post('check_room_filter_date', 'Mobile\Modules\Accommodation\AccommodationBookingAPIController@checkRoomFilterDate');
Route::post('booking_accommodation_room', 'Mobile\Modules\Accommodation\AccommodationBookingAPIController@bookingAccommodationRoom');
Route::post('get_sale_list_accommodation', 'Mobile\Modules\Accommodation\AccommodationBookingAPIController@getSaleListAccommodation');
Route::post('get_sale_accommodation_detail', 'Mobile\Modules\Accommodation\AccommodationBookingAPIController@getSaleAccommodationDetail');
Route::post('change_status_sale_accommodation', 'Mobile\Modules\Accommodation\AccommodationBookingAPIController@changeStatusSaleAccommodation');
Route::post('add_payment_accommodation', 'Mobile\Modules\Accommodation\AccommodationBookingAPIController@addPaymentAccommodation');

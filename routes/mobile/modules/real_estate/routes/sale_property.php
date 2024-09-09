<?php

Route::post('property_booking', 'Mobile\Modules\RealEstate\SalePropertyAPIController@propertyBooking');
Route::post('get_sale_property_list', 'Mobile\Modules\RealEstate\SalePropertyAPIController@getSaleListProperty');
Route::post('get_sale_property_detail', 'Mobile\Modules\RealEstate\SalePropertyAPIController@getSaleDetailProperty');
Route::post('change_sale_property_status', 'Mobile\Modules\RealEstate\SalePropertyAPIController@changeStatusSaleProperty');

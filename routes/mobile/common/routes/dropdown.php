<?php

Route::post('get_province_list', 'Mobile\Common\DropdownAPIController@getProvinceList');
Route::post('get_district_list_by_province', 'Mobile\Common\DropdownAPIController@getDistrictListByProvince');
Route::post('get_commune_list_by_district', 'Mobile\Common\DropdownAPIController@getCommuneListByDistrict');
Route::post('get_property_type_list', 'Mobile\Common\DropdownAPIController@getPropertyTypeList');
Route::post('get_app_type_list', 'Mobile\Common\DropdownAPIController@getAppTypeList');
Route::post('get_business_type_list', 'Mobile\Common\DropdownAPIController@getBusinessTypeList');
Route::post('get_business_type_has_transaction_list', 'Mobile\Common\DropdownAPIController@getBusinessTypeHasTransactionList');
Route::post('get_agency_list', 'Mobile\Common\DropdownAPIController@getAgencyList');
Route::post('get_bank_list', 'Mobile\Common\DropdownAPIController@getBankList');
Route::post('get_social_contact_list', 'Mobile\Common\DropdownAPIController@getSocialContactList');
Route::post('get_shop_category_list', 'Mobile\Common\DropdownAPIController@getShopCategoryList');
Route::post('get_product_category_by_id', 'Mobile\Common\DropdownAPIController@getProductCategoryByID');
Route::post('get_attribute_list', 'Mobile\Common\DropdownAPIController@getAttributeList');
Route::post('get_massager_list', 'Mobile\Common\DropdownAPIController@getMassageTherapistList');
Route::post('change_active_customer_sale_list', 'Mobile\Common\DropdownAPIController@changeActiveCustomerSaleList');
Route::post('get_recipient_list', 'Mobile\Common\DropdownAPIController@getRecipientList');
Route::post('get_recipient_detail', 'Mobile\Common\DropdownAPIController@getRecipientDetail');
Route::post('get_ktv_girl_list', 'Mobile\Common\DropdownAPIController@getKtvGirlList');
Route::post('get_api_version_value', 'Mobile\Common\DropdownAPIController@getAPIVersionValue');
Route::post('get_charity_category_list', 'Mobile\Common\DropdownAPIController@getCharityCategoryList');
Route::post('get_vehicle_type_list', 'Mobile\Common\DropdownAPIController@getVehicleTypeList');
Route::post('get_contact_list_detail_chat', 'Mobile\Common\DropdownAPIController@getContactListDetailChat');
Route::post('get_driver_list', 'Mobile\Common\DropdownAPIController@getDriverList');
Route::post('get_item_type_list', 'Mobile\Common\DropdownAPIController@getItemTypeList');

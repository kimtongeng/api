<?php
Route::post('get_contact_list', 'Mobile\Common\ShareBusinessAPIController@getContactList');
Route::post('get_business_permission', 'Mobile\Common\ShareBusinessAPIController@getBusinessPermissionByBusinessType');
Route::post('get_business_contact_permission', 'Mobile\Common\ShareBusinessAPIController@getBusinessContactPermission');
Route::post('share_business_to_contact', 'Mobile\Common\ShareBusinessAPIController@shareBusinessToContact');
Route::post('delete_share_business_to_contact', 'Mobile\Common\ShareBusinessAPIController@deleteShareBusinessToContact');
Route::post('check_contact_has_permission', 'Mobile\Common\ShareBusinessAPIController@checkContactHasPermission');

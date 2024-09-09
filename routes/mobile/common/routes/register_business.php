<?php
Route::post('become_agency_or_update_info', 'Mobile\Common\RegisterBusinessAPIController@becomeAgencyOrUpdateInfo');
Route::post('become_massager_or_update_info', 'Mobile\Common\RegisterBusinessAPIController@becomeMassagerOrUpdateInfo');
Route::post('become_recipient_or_update_info', 'Mobile\Common\RegisterBusinessAPIController@becomeRecipientOrUpdateInfo');
Route::post('become_ktv_girl_or_update_info', 'Mobile\Common\RegisterBusinessAPIController@becomeKtvGirlOrUpdateInfo');
Route::post('become_driver_or_update_info', 'Mobile\Common\RegisterBusinessAPIController@becomeDriverOrUpdateInfo');

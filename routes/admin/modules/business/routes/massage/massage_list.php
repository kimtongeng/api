<?php

Route::post('massage_list/get', 'Admin\Modules\Business\Massage\MassageListController@get');
Route::post('massage_list/get_detail', 'Admin\Modules\Business\Massage\MassageListController@getDetail');
Route::post('massage_list/change_active', 'Admin\Modules\Business\Massage\MassageListController@changeActive');
Route::post('massage_list/update_verify', 'Admin\Modules\Business\Massage\MassageListController@updateVerify');
Route::post('massage_list/get_select_data', 'Admin\Modules\Business\Massage\MassageListController@getSelectData');
Route::post('massage_list/get_select_massage_detail', 'Admin\Modules\Business\Massage\MassageListController@getSelectMassageDetail');
Route::post('service_list/get_service_list', 'Admin\Modules\Business\Massage\MassageListController@getServiceList');
Route::post('service_list/change_status_suspend', 'Admin\Modules\Business\Massage\MassageListController@changeStatusSuspend');
Route::post('massage_therapist/get_massage_therapist_list', 'Admin\Modules\Business\Massage\MassageListController@getMassageTherapistList');

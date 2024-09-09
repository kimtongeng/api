<?php

Route::post('charity_list/get', 'Admin\Modules\Business\Charity\CharityListController@get');
Route::post('charity_list/get_detail', 'Admin\Modules\Business\Charity\CharityListController@getDetail');
Route::post('charity_list/get_select_data', 'Admin\Modules\Business\Charity\CharityListController@getSelectData');
Route::post('charity_list/change_active', 'Admin\Modules\Business\Charity\CharityListController@changeActive');
Route::post('charity_list/update_verify', 'Admin\Modules\Business\Charity\CharityListController@updateVerify');

<?php
Route::post('attraction_list/get', 'Admin\Modules\Business\AttractionListController@get');
Route::post('attraction_list/get_detail', 'Admin\Modules\Business\AttractionListController@getDetail');
Route::post('attraction_list/change_active', 'Admin\Modules\Business\AttractionListController@changeActive');
Route::post('attraction_list/update_verify', 'Admin\Modules\Business\AttractionListController@updateVerify');
Route::post('attraction_list/get_select_data', 'Admin\Modules\Business\AttractionListController@getSelectData');

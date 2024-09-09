<?php
Route::post('property_list/get', 'Admin\Modules\Business\PropertyListController@get');
Route::post('property_list/get_detail', 'Admin\Modules\Business\PropertyListController@getDetail');
Route::post( 'property_list/change_active', 'Admin\Modules\Business\PropertyListController@changeActive');
Route::post('property_list/update_verify', 'Admin\Modules\Business\PropertyListController@updateVerify');
Route::post('property_list/get_asset_list', 'Admin\Modules\Business\PropertyListController@getAssetList');
Route::post('property_list/get_select_data', 'Admin\Modules\Business\PropertyListController@getSelectData');

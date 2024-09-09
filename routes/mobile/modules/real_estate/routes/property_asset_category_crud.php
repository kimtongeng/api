<?php

Route::post('get_asset_category_list', 'Mobile\Modules\RealEstate\PropertyAssetCategoryCrudAPIController@getAssetCategoryList');
Route::post('add_asset_category', 'Mobile\Modules\RealEstate\PropertyAssetCategoryCrudAPIController@addAssetCategory');
Route::post('edit_asset_category', 'Mobile\Modules\RealEstate\PropertyAssetCategoryCrudAPIController@editAssetCategory');
Route::post('delete_asset_category', 'Mobile\Modules\RealEstate\PropertyAssetCategoryCrudAPIController@deleteAssetCategory');

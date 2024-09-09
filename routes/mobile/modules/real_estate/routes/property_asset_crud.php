<?php

Route::post('add_asset', 'Mobile\Modules\RealEstate\PropertyAssetCrudAPIController@addAsset');
Route::post('edit_asset', 'Mobile\Modules\RealEstate\PropertyAssetCrudAPIController@editAsset');
Route::post('delete_asset', 'Mobile\Modules\RealEstate\PropertyAssetCrudAPIController@deleteAsset');
Route::post('get_asset_list', 'Mobile\Modules\RealEstate\PropertyAssetCrudAPIController@getAssetList');

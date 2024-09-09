<?php

Route::post('get_commission_property_list', 'Mobile\Modules\RealEstate\CommissionPropertyAPIController@getCommissionPropertyList');
Route::post('get_commission_property_asset_list', 'Mobile\Modules\RealEstate\CommissionPropertyAPIController@getCommissionPropertyAssetList');
Route::post('withdraw_commission_property', 'Mobile\Modules\RealEstate\CommissionPropertyAPIController@withdrawCommissionProperty');
Route::post('get_withdrawn_commission_property_list', 'Mobile\Modules\RealEstate\CommissionPropertyAPIController@getWithdrawnCommissionPropertyList');
Route::post('confirm_reject_withdrawn_commission_property', 'Mobile\Modules\RealEstate\CommissionPropertyAPIController@confirmRejectWithdrawnCommissionProperty');
Route::post('count_pending_withdrawing_property_commission', 'Mobile\Modules\RealEstate\CommissionPropertyAPIController@countPendingWithdrawingPropertyCommission');

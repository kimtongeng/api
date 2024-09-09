<?php
Route::post('get_transaction_fee_list', 'Mobile\Common\TransactionFeeAPIController@getTransactionFeeList');
Route::post('repayment_transaction_fee', 'Mobile\Common\TransactionFeeAPIController@repaymentTransactionFee');
Route::post('get_transaction_fee_payment_history', 'Mobile\Common\TransactionFeeAPIController@getTransactionFeePaymentHistory');
Route::post('get_count_transaction_fee_by_business', 'Mobile\Common\TransactionFeeAPIController@getCountTransactionFeeByBusiness');
Route::post('get_property_by_transaction_fee_date', 'Mobile\Common\TransactionFeeAPIController@getPropertyByTransactionFeeDate');
Route::post('get_shop_by_transaction_fee_date', 'Mobile\Common\TransactionFeeAPIController@getShopByTransactionFeeDate');
Route::post('get_accommodation_by_transaction_fee_date', 'Mobile\Common\TransactionFeeAPIController@getAccommodationByTransactionFeeDate');
Route::post('get_massage_by_transaction_fee_date', 'Mobile\Common\TransactionFeeAPIController@getMassageByTransactionFeeDate');
Route::post('get_attraction_by_transaction_fee_date', 'Mobile\Common\TransactionFeeAPIController@getAttractionByTransactionFeeDate');
Route::post('get_ktv_by_transaction_fee_date', 'Mobile\Common\TransactionFeeAPIController@getKTVByTransactionFeeDate');

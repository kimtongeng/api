<?php
Route::post('add_bank_account', 'Mobile\Common\BankAccountAPIController@addBankAccount');
Route::post('get_my_bank_account', 'Mobile\Common\BankAccountAPIController@getMyBankAccount');
Route::post('update_bank_account', 'Mobile\Common\BankAccountAPIController@updateBankAccount');
Route::post('remove_bank_account', 'Mobile\Common\BankAccountAPIController@removeBankAccount');
Route::post('get_bank_account_by_business', 'Mobile\Common\BankAccountAPIController@getBankAccountByBusiness');
Route::post('get_bank_account_by_agency', 'Mobile\Common\BankAccountAPIController@getBankAccountByAgency');
Route::post('get_bank_account_admin', 'Mobile\Common\BankAccountAPIController@getBankAccountAdmin');
Route::post('get_bank_account_list_property_agency', 'Mobile\Common\BankAccountAPIController@getBankAccountListPropertyAgency');

<?php
Route::post('bank_account/get', 'Admin\Modules\Setting\BankAccountController@get');
Route::post('bank_account/store', 'Admin\Modules\Setting\BankAccountController@store');
Route::post('bank_account/delete', 'Admin\Modules\Setting\BankAccountController@delete');
Route::post('bank_account/update', 'Admin\Modules\Setting\BankAccountController@update');
Route::post('bank_account/get_bank_list', 'Admin\Modules\Setting\BankAccountController@getBankList');
Route::post('bank_account/change_status', 'Admin\Modules\Setting\BankAccountController@changeStatus');

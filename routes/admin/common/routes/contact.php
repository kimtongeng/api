<?php
Route::post('contact/get', 'Admin\Common\ContactController@get');
Route::post('contact/get_combo_list', 'Admin\Common\ContactController@getComboList');
Route::post('contact/store', 'Admin\Common\ContactController@store');
Route::post('contact/delete', 'Admin\Common\ContactController@delete');
Route::post('contact/update', 'Admin\Common\ContactController@update');
Route::post('contact/change_status', 'Admin\Common\ContactController@changeStatus');
Route::post('contact/get_device', 'Admin\Common\ContactController@getDevice');
Route::post('contact/block_business', 'Admin\Common\ContactController@blockBusiness');

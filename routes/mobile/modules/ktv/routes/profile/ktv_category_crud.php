<?php

Route::post('add_ktv_category', 'Mobile\Modules\KTV\Profile\KTVCategoryCrudAPIController@addKTVCategory');
Route::post('edit_ktv_category', 'Mobile\Modules\KTV\Profile\KTVCategoryCrudAPIController@editKTVCategory');
Route::post('delete_ktv_category', 'Mobile\Modules\KTV\Profile\KTVCategoryCrudAPIController@deleteKTVCategory');
Route::post('get_my_ktv_category', 'Mobile\Modules\KTV\Profile\KTVCategoryCrudAPIController@getMyKTVCategory');

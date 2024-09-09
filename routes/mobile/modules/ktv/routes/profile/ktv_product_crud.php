<?php

Route::post('add_ktv_product', 'Mobile\Modules\KTV\Profile\KTVProductCrudAPIController@addKTVProduct');
Route::post('edit_ktv_product', 'Mobile\Modules\KTV\Profile\KTVProductCrudAPIController@editKTVProduct');
Route::post('delete_ktv_product', 'Mobile\Modules\KTV\Profile\KTVProductCrudAPIController@deleteKTVProduct');
Route::post('get_my_ktv_product', 'Mobile\Modules\KTV\Profile\KTVProductCrudAPIController@getMyKTVProduct');

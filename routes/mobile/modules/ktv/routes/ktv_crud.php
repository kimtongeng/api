<?php

Route::post('add_ktv', 'Mobile\Modules\KTV\KTVCrudAPIController@addKTV');
Route::post('edit_ktv', 'Mobile\Modules\KTV\KTVCrudAPIController@editKTV');
Route::post('delete_ktv', 'Mobile\Modules\KTV\KTVCrudAPIController@deleteKTV');
Route::post('get_my_ktv', 'Mobile\Modules\KTV\KTVCrudAPIController@getMyKTV');

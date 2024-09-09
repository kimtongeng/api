<?php

Route::post('add_ktv_girl', 'Mobile\Modules\KTV\Profile\KTVGirlCrudAPIController@addKTVGirl');
Route::post('edit_ktv_girl', 'Mobile\Modules\KTV\Profile\KTVGirlCrudAPIController@editKTVGirl');
Route::post('delete_ktv_girl', 'Mobile\Modules\KTV\Profile\KTVGirlCrudAPIController@deleteKTVGirl');
Route::post('get_my_ktv_girl', 'Mobile\Modules\KTV\Profile\KTVGirlCrudAPIController@getMyKTVGirl');
Route::post('change_status_ktv_girl', 'Mobile\Modules\KTV\Profile\KTVGirlCrudAPIController@changeStatusKTVGirl');

<?php
Route::post('video/get', 'Admin\Modules\Video\VideoController@get');
Route::post('video/store', 'Admin\Modules\Video\VideoController@store');
Route::post('video/delete', 'Admin\Modules\Video\VideoController@delete');
Route::post('video/update', 'Admin\Modules\Video\VideoController@update');
Route::post('video/change_status', 'Admin\Modules\Video\VideoController@changeStatus');
Route::post('video/get_auto_order', 'Admin\Modules\Video\VideoController@getAutoOrder');

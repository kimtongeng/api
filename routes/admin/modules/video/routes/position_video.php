<?php
Route::post('position_video/get', 'Admin\Modules\Video\PositionVideoController@get');
Route::post('position_video/get_select_data', 'Admin\Modules\Video\PositionVideoController@getSelectData');
Route::post('position_video/store', 'Admin\Modules\Video\PositionVideoController@store');
Route::post('position_video/delete', 'Admin\Modules\Video\PositionVideoController@delete');
Route::post('position_video/edit', 'Admin\Modules\Video\PositionVideoController@edit');
Route::post('position_video/update', 'Admin\Modules\Video\PositionVideoController@update');
Route::post('position_video/get_video_list', 'Admin\Modules\Video\PositionVideoController@getVideoList');
Route::post('position_video/change_status', 'Admin\Modules\Video\PositionVideoController@changeStatus');

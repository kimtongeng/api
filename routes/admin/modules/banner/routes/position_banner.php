<?php
Route::post('position_banner/get', 'Admin\Modules\Banner\PositionBannerController@get');
Route::post('position_banner/get_select_data', 'Admin\Modules\Banner\PositionBannerController@getSelectData');
Route::post('position_banner/store', 'Admin\Modules\Banner\PositionBannerController@store');
Route::post('position_banner/delete', 'Admin\Modules\Banner\PositionBannerController@delete');
Route::post('position_banner/edit', 'Admin\Modules\Banner\PositionBannerController@edit');
Route::post('position_banner/update', 'Admin\Modules\Banner\PositionBannerController@update');
Route::post('position_banner/get_banner_list', 'Admin\Modules\Banner\PositionBannerController@getBannerList');
Route::post('position_banner/change_status', 'Admin\Modules\Banner\PositionBannerController@changeStatus');

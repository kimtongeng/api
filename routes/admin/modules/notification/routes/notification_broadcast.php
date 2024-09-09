<?php
Route::post('broadcast/get', 'Admin\Modules\Notification\NotificationBroadcastController@get');
Route::post('broadcast/store_or_update', 'Admin\Modules\Notification\NotificationBroadcastController@storeOrUpdate');
Route::post('broadcast/edit', 'Admin\Modules\Notification\NotificationBroadcastController@getEditData');
Route::post('broadcast/delete', 'Admin\Modules\Notification\NotificationBroadcastController@delete');
Route::post('broadcast/resend', 'Admin\Modules\Notification\NotificationBroadcastController@resend');
Route::post('broadcast/get_select_data', 'Admin\Modules\Notification\NotificationBroadcastController@getSelectData');

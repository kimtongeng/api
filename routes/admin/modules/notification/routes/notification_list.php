<?php
Route::post('notifications/list', 'Admin\Modules\Notification\NotificationAdminListController@get');
Route::post('notifications/set_readed', 'Admin\Modules\Notification\NotificationAdminListController@setReaded');
Route::post('notifications/get_badge_data', 'Admin\Modules\Notification\NotificationAdminListController@getBadgeData');
Route::post('notifications/get_transaction_detail', 'Admin\Modules\Notification\NotificationAdminListController@getTransactionDetail');

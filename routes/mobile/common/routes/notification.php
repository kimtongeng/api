<?php
Route::post('get_notification_list', 'Mobile\Common\NotificationAPIController@getNotificationList');
Route::post('get_notification_detail', 'Mobile\Common\NotificationAPIController@getNotificationDetail');
Route::post('set_notification_read', 'Mobile\Common\NotificationAPIController@setNotificationRead');
Route::post('mark_all_notification_as_read', 'Mobile\Common\NotificationAPIController@markAllNotificationsAsRead');
Route::post('send_chat_notification', 'Mobile\Common\NotificationAPIController@sendChatNotificationToAdmin');

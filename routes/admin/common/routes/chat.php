<?php
Route::post('chat/get_admin_list', 'Admin\Common\ChatController@getAdminList');
Route::post('chat/get_customer_list', 'Admin\Common\ChatController@getCustomerList');
Route::post('chat/send_notification_to_app', 'Admin\Common\ChatController@sendNotificationToApp');

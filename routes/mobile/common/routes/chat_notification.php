<?php

Route::post('create_device_group', 'Mobile\Common\ChatNotificationAPIController@createDeviceGroup');
Route::post('add_user_to_group', 'Mobile\Common\ChatNotificationAPIController@addUserToDeviceGroup');
Route::post('send_chat_notification_to', 'Mobile\Common\ChatNotificationAPIController@sendChatNotificationTo');

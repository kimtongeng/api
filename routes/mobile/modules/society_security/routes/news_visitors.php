<?php

Route::post('add_news_visitors', 'Mobile\Modules\SocietySecurity\NewsVisitorsCrudController@addNewsVisitors');
Route::post('join_conversation', 'Mobile\Modules\SocietySecurity\NewsVisitorsCrudController@joinConversation');
Route::post('leave_conversation', 'Mobile\Modules\SocietySecurity\NewsVisitorsCrudController@leaveConversation');
Route::post('get_news_visitors_list', 'Mobile\Modules\SocietySecurity\NewsVisitorsCrudController@getNewsVisitorsList');

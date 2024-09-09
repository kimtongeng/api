<?php

Route::post('get_news_detail', 'Mobile\Modules\SocietySecurity\NewsListAPIController@getNewsDetail');
Route::post('get_news_list_for_recipient', 'Mobile\Modules\SocietySecurity\NewsListAPIController@getNewsListForRecipient');

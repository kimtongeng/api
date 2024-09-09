<?php

Route::post('add_news', 'Mobile\Modules\SocietySecurity\NewsCrudAPIController@addNews');
Route::post('edit_news', 'Mobile\Modules\SocietySecurity\NewsCrudAPIController@editNews');
Route::post('delete_news', 'Mobile\Modules\SocietySecurity\NewsCrudAPIController@deleteNews');
Route::post('get_news_lists', 'Mobile\Modules\SocietySecurity\NewsCrudAPIController@getNewsList');

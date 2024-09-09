<?php

Route::post('add_comment', 'Mobile\Modules\SocietySecurity\NewsCommentCrudController@addComment');
Route::post('get_comment_list', 'Mobile\Modules\SocietySecurity\NewsCommentCrudController@getCommentList');

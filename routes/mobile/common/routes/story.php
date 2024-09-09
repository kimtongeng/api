<?php

Route::post( 'get_business_type_list_by_contact', 'Mobile\Common\StoryAPIController@getBusinessTypeListByContact');
Route::post('get_business_list_by_business_type', 'Mobile\Common\StoryAPIController@getBusinessListByBusinessType');
Route::post('add_new_story', 'Mobile\Common\StoryAPIController@addNewStory');

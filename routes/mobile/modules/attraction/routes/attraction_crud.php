<?php

Route::post('add_attraction', 'Mobile\Modules\Attraction\AttractionCrudAPIController@addAttraction');
Route::post('edit_attraction', 'Mobile\Modules\Attraction\AttractionCrudAPIController@editAttraction');
Route::post('delete_attraction', 'Mobile\Modules\Attraction\AttractionCrudAPIController@deleteAttraction');
Route::post('get_my_attraction', 'Mobile\Modules\Attraction\AttractionCrudAPIController@getMyAttraction');

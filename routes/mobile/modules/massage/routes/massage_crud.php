<?php

Route::post('add_massage', 'Mobile\Modules\Massage\MassageCrudAPIController@addMassage');
Route::post('edit_massage', 'Mobile\Modules\Massage\MassageCrudAPIController@editMassage');
Route::post('delete_massage', 'Mobile\Modules\Massage\MassageCrudAPIController@deleteMassage');
Route::post('get_my_massage', 'Mobile\Modules\Massage\MassageCrudAPIController@getMyMassage');

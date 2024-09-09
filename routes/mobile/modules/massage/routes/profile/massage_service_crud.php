<?php

Route::post('add_massage_service', 'Mobile\Modules\Massage\Profile\MassageServiceCrudAPIController@addMassageService');
Route::post('edit_massage_service', 'Mobile\Modules\Massage\Profile\MassageServiceCrudAPIController@editMassageService');
Route::post('delete_massage_service', 'Mobile\Modules\Massage\Profile\MassageServiceCrudAPIController@deleteMassageService');
Route::post('get_massage_service', 'Mobile\Modules\Massage\Profile\MassageServiceCrudAPIController@getMassageService');

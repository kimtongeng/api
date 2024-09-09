<?php

Route::post('add_massage_therapist', 'Mobile\Modules\Massage\Profile\MassageTherapistCrudAPIController@addMassageTherapist');
Route::post('edit_massage_therapist', 'Mobile\Modules\Massage\Profile\MassageTherapistCrudAPIController@editMassageTherapist');
Route::post('delete_massage_therapist', 'Mobile\Modules\Massage\Profile\MassageTherapistCrudAPIController@deleteMassageTherapist');
Route::post('get_massage_therapist', 'Mobile\Modules\Massage\Profile\MassageTherapistCrudAPIController@getMassageTherapist');
Route::post('change_status_massage_therapist', 'Mobile\Modules\Massage\Profile\MassageTherapistCrudAPIController@changeStatusMassageTherapist');

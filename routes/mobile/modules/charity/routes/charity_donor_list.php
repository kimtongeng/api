<?php

Route::post('get_charity_donor_list', 'Mobile\Modules\Charity\CharityDonorListAPIController@getCharityDonorList');
Route::post('get_charity_donor_detail', 'Mobile\Modules\Charity\CharityDonorListAPIController@getCharityDonorDetail');
Route::post('change_status_charity_donor', 'Mobile\Modules\Charity\CharityDonorListAPIController@changeStatusCharityDonor');
Route::post('change_active_charity_donor', 'Mobile\Modules\Charity\CharityDonorListAPIController@changeActiveCharityDonorList');

<?php

Route::post('get_charity_organization_list', 'Mobile\Modules\Charity\CharityHomeScreenAPIController@getCharityOrganizationList');
Route::post('donation_charity', 'Mobile\Modules\Charity\CharityHomeScreenAPIController@donationCharity');
Route::post('get_charity_has_donation_list', 'Mobile\Modules\Charity\CharityHomeScreenAPIController@getCharityHasDonationList');
Route::post('get_charity_has_donation_detail', 'Mobile\Modules\Charity\CharityHomeScreenAPIController@getCharityHasDonationDetail');

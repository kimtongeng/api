<?php

Route::post('add_charity_organization', 'Mobile\Modules\Charity\CharityOrganizationCrudAPIController@addCharityOrganization');
Route::post('edit_charity_organization', 'Mobile\Modules\Charity\CharityOrganizationCrudAPIController@editCharityOrganization');
Route::post('delete_charity_organization', 'Mobile\Modules\Charity\CharityOrganizationCrudAPIController@deleteCharityOrganization');
Route::post('get_my_charity_organization', 'Mobile\Modules\Charity\CharityOrganizationCrudAPIController@getMyCharityOrganization');
Route::post('get_charity_organization_detail', 'Mobile\Modules\Charity\CharityOrganizationCrudAPIController@getCharityOrganizationDetail');

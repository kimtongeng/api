<?php

Route::post('dropdown/get_country_list', 'Admin\Common\DropdownController@getCountryList');
Route::post('dropdown/get_province_by_country', 'Admin\Common\DropdownController@getProvinceListByCountry');
Route::post('dropdown/get_district_by_province', 'Admin\Common\DropdownController@getDistrictListByProvince');
Route::post('dropdown/get_commune_by_district', 'Admin\Common\DropdownController@getCommuneListByDistrict');

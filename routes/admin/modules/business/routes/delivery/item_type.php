<?php

Route::post('item_type/get', 'Admin\Modules\Business\Delivery\ItemTypeListController@get');
Route::post('item_type/store', 'Admin\Modules\Business\Delivery\ItemTypeListController@store');
Route::post('item_type/update', 'Admin\Modules\Business\Delivery\ItemTypeListController@update');
Route::post('item_type/delete', 'Admin\Modules\Business\Delivery\ItemTypeListController@delete');
Route::post('item_type/get_auto_order', 'Admin\Modules\Business\Delivery\ItemTypeListController@getAutoOrder');


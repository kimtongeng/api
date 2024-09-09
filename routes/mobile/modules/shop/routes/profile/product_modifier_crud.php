<?php

Route::post('add_product_modifier', 'Mobile\Modules\Shop\Profile\ProductModifierCrudAPIController@addModifier');
Route::post('edit_product_modifier', 'Mobile\Modules\Shop\Profile\ProductModifierCrudAPIController@editModifier');
Route::post('delete_product_modifier', 'Mobile\Modules\Shop\Profile\ProductModifierCrudAPIController@deleteModifier');
Route::post('get_my_product_modifier', 'Mobile\Modules\Shop\Profile\ProductModifierCrudAPIController@getMyModifier');

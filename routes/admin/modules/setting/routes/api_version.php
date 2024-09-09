<?php

Route::post('api_version/get', 'Admin\Modules\Setting\APIVersionController@get');
Route::post('api_version/update', 'Admin\Modules\Setting\APIVersionController@update');

<?php

//Post
Route::post('privacy_policy/get', 'Admin\Modules\Setting\PrivacyPolicyController@get');
Route::post('privacy_policy/update', 'Admin\Modules\Setting\PrivacyPolicyController@update');

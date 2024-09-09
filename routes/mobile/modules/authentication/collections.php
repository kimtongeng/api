<?php

/**
 * No Authentication
 */
$router->group(['prefix' => 'mobile'], function () use ($router) {
    Route::post('login', 'Mobile\Modules\Authentication\AuthController@login');
    Route::post('request_otp_code', 'Mobile\Modules\Authentication\AuthController@requestOTPCode');
    Route::post('resend_otp_code', 'Mobile\Modules\Authentication\AuthController@resendOTPCode');
    Route::post('activate_account', 'Mobile\Modules\Authentication\AuthController@activateAccount');
});


/**
 * Have Authentication
 */
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    Route::post('add_or_update_token', 'Mobile\Modules\Authentication\AuthController@addOrUpdateFCMToken');
    Route::post('get_current_user', 'Mobile\Modules\Authentication\AuthController@getCurrentUser');
    Route::post('deactivate_account', 'Mobile\Modules\Authentication\AuthController@deactivateAccount');
    Route::post('delete_account', 'Mobile\Modules\Authentication\AuthController@deleteAccount');
    Route::post('logout', 'Mobile\Modules\Authentication\AuthController@logout');
});

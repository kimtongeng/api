<?php

namespace App\Helpers\Utils;

class ErrorCode
{

    const PHONE_EXISTS = [
        'code' => '001',
        'message' => 'phone_exists',
    ];
    const EMAIL_EXISTS = [
        'code' => '002',
        'message' => 'email_exists',
    ];
    const GOOGLE_EXISTS = [
        'code' => '003',
        'message' => 'google_exists',
    ];
    const SOCIAL_EXISTS = [
        'code' => '004',
        'message' => 'social_exists',
    ];
    const APPLE_ID_EXISTS = [
        'code' => '005',
        'message' => 'apple_id_exists',
    ];
    const ACCOUNT_NOT_VERIFIED = [
        'code' => '006',
        'message' => 'account_not_verified',
    ];
    const UNAUTHORIZED = [
        'code' => '007',
        'message' => 'unauthorized',
    ];
    const INVALID = [
        'code' => '008',
        'message' => 'invalid',
    ];

    const ACTION_FAILED = [
        'code' => '100',
        'message' => 'the_request_action_failed',
    ];

    const REGISTER_FAIL = [
        'code' => '111',
        'message' => 'register_failed',
    ];
    const LOGIN_FAIL = [
        'code' => '112',
        'message' => 'login_failed',
    ];
    const BUSINESS_IS_CLOSE = [
        'code' => '113',
        'message' => 'business_is_close',
    ];
    const MEKONG_SMS_FAILED = [
        'code' => '114',
        'message' => 'mekong_sms_failed',
    ];
}

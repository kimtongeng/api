<?php

namespace App\Enums\Types;

class ContactLoginType
{
    //Declare Name And Value
    const PHONE = [
        'id' => 1,
        'name' => 'PHONE'
    ];
    const GOOGLE = [
        'id' => 2,
        'name' => 'GOOGLE'
    ];
    const FACEBOOK = [
        'id' => 3,
        'name' => 'FACEBOOK'
    ];
    const APPLE_ID = [
        'id' => 4,
        'name' => 'APPLE_ID'
    ];

    //Get Value By Function Name (For Api)
    public static function byPhone()
    {
        return self::PHONE['id'];
    }
    public static function byGoogle()
    {
        return self::GOOGLE['id'];
    }
    public static function byFacebook()
    {
        return self::FACEBOOK['id'];
    }
    public static function byAppleId()
    {
        return self::APPLE_ID['id'];
    }
}

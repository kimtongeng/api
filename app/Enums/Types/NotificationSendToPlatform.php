<?php

namespace App\Enums\Types;


class NotificationSendToPlatform
{
    //Declare Name And Value
    const WEB = [
        'id' => 1,
        'name' => 'WEB'
    ];
    const MOBILE = [
        'id' => 2,
        'name' => 'MOBILE'
    ];

    //Get Value By Function Name (For Api)
    public static function getWeb()
    {
        return self::WEB['id'];
    }
    public static function getMobile()
    {
        return self::MOBILE['id'];
    }
}

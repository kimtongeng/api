<?php

namespace App\Enums\Types;

class PositionPlatformType
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

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::WEB,
            self::MOBILE
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getWeb()
    {
        return self::WEB['id'];
    }
    public static function getMobile()
    {
        return self::MOBILE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::WEB['name'] => self::WEB['id'],
            self::MOBILE['name'] => self::MOBILE['id']
        ];
    }
}

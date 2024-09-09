<?php

namespace App\Enums\Types;

class PositionType
{
    //Declare Name And Value
    const BANNER = [
        'id' => 1,
        'name' => 'BANNER'
    ];
    const VIDEO = [
        'id' => 2,
        'name' => 'VIDEO'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::BANNER,
            self::VIDEO
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getBanner()
    {
        return self::BANNER['id'];
    }

    public static function getVideo()
    {
        return self::VIDEO['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::BANNER['name'] => self::BANNER['id'],
            self::VIDEO['name'] => self::VIDEO['id']
        ];
    }
}

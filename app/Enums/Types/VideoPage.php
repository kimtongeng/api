<?php

namespace App\Enums\Types;

class VideoPage
{
    //Declare Name And Value
    const HOME = [
        'id' => 1,
        'name' => 'home'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            PositionPlatformType::MOBILE['name'] => [
                self::HOME
            ],
            PositionPlatformType::WEB['name'] => [
            ]
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getHomePage()
    {
        return self::HOME['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            strtoupper(self::HOME['name']) => self::HOME['id']
        ];
    }
}

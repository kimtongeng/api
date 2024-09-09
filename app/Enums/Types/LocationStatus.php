<?php

namespace App\Enums\Types;

class LocationStatus 
{
    //Declare Name And Value
    const DISABLED = [
        'id' => 0,
        'name' => 'DISABLED'
    ];
    const ENABLED = [
        'id' => 1,
        'name' => 'ENABLED'
    ];

    //Get Value By Function Name (For Api)
    public static function getDisabled()
    {
        return self::DISABLED['id'];
    }

    public static function getEnabled()
    {
        return self::ENABLED['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::ENABLED['name'] => self::ENABLED['id'],
            self::DISABLED['name'] => self::DISABLED['id'],
        ];
    }
}
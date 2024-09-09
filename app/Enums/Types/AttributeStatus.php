<?php

namespace App\Enums\Types;

class AttributeStatus 
{
    //Declare Name And Value
    const DISABLE = [
        'id' => 0,
        'name' => 'DISABLE'
    ];
    const ENABLE = [
        'id' => 1,
        'name' => 'ENABLE'
    ];

    //Get Value By Function Name (For Api)
    public static function getDisabled()
    {
        return self::DISABLE['id'];
    }

    public static function getEnabled()
    {
        return self::ENABLE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::ENABLE['name'] => self::ENABLE['id'],
            self::DISABLE['name'] => self::DISABLE['id']
        ];
    }
}
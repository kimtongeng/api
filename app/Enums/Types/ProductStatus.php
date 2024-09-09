<?php

namespace App\Enums\Types;

class ProductStatus
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
    const SUSPEND = [
        'id' => 2,
        'name' => 'SUSPEND'
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

    public static function getSuspend()
    {
        return self::SUSPEND['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::ENABLE['name'] => self::ENABLE['id'],
            self::DISABLE['name'] => self::DISABLE['id'],
            self::SUSPEND['name'] => self::SUSPEND['id']
        ];
    }
}

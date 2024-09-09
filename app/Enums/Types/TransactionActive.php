<?php

namespace App\Enums\Types;

class TransactionActive
{
    //Declare Name and Value
    const ENABLE = [
        'id' => 1,
        'name' => 'ENABLE',
    ];
    const DISABLE = [
        'id' => 0,
        'name' => 'DISABLE',
    ];

    //Get Value By Function Name (For Api)
    public static function getEnable()
    {
        return self::ENABLE['id'];
    }
    public static function getDisable()
    {
        return self::DISABLE['id'];
    }
}

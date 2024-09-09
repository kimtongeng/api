<?php

namespace App\Enums\Types;

class ContactType
{
    //Declare Name And Value
    const SINGLE = [
        'id' => 1,
        'name' => 'SINGLE',
    ];
    const BROADCAST = [
        'id' => 2,
        'name' => 'BROADCAST',
    ];

    //Get Value By Function Name (For Api)
    public static function getSingleType()
    {
        return self::SINGLE['id'];
    }
    public static function getBroadcastType()
    {
        return self::BROADCAST['id'];
    }
}

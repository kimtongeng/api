<?php

namespace App\Enums\Types;

class ContactRegisterType
{
    //Declare Name And Value
    const SELLER = [
        'id' => 1,
        'name' => 'SELLER'
    ];
    const AGENCY = [
        'id' => 2,
        'name' => 'AGENCY'
    ];

    //Get Value By Function Name (For Api)
    public static function seller()
    {
        return self::SELLER['id'];
    }
    public static function agency()
    {
        return self::AGENCY['id'];
    }
}

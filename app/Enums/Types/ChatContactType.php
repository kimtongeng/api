<?php

namespace App\Enums\Types;

class ChatContactType
{
    //Declare Name And Value
    const AGENCY = [
        'id' => 1,
        'name' => 'AGENCY',
    ];
    const OWNER = [
        'id' => 2,
        'name' => 'OWNER',
    ];
    const CONTACT_SHARE = [
        'id' => 3,
        'name' => 'CONTACT_SHARE',
    ];

    //Get Value By Function Name (For Api)
    public static function getAgency()
    {
        return self::AGENCY['id'];
    }
    public static function getOwner()
    {
        return self::OWNER['id'];
    }
    public static function getContactShare()
    {
        return self::CONTACT_SHARE['id'];
    }
}

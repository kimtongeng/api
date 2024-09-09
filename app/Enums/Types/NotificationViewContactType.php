<?php

namespace App\Enums\Types;

class NotificationViewContactType
{
    //Declare Name And Value
    const CONTACT = [
        'id' => 1,
        'name' => 'CONTACT'
    ];
    const ADMIN = [
        'id' => 2,
        'name' => 'ADMIN'
    ];


    //Get Value By Function Name (For Api)
    public static function getContact()
    {
        return self::CONTACT['id'];
    }

    public static function getAdmin()
    {
        return self::ADMIN['id'];
    }
}

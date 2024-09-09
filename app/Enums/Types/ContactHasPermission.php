<?php

namespace App\Enums\Types;

class ContactHasPermission
{
    //Declare Name And Value
    const NO = [
        'id' => 0,
        'name' => 'NO'
    ];
    const YES = [
        'id' => 1,
        'name' => 'YES'
    ];
    const NOT_SET_PERMISSION = [
        'id' => 2,
        'name' => 'NOT_SET_PERMISSION'
    ];

    //Get Value By Function Name (For Api)
    public static function getNo()
    {
        return self::NO['id'];
    }

    public static function getYes()
    {
        return self::YES['id'];
    }

    public static function getNotSetPermission()
    {
        return self::NOT_SET_PERMISSION['id'];
    }
}

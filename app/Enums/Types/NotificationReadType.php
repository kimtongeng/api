<?php

namespace App\Enums\Types;


class NotificationReadType
{
    //Declare Name And Value
    const NOT_READ = [
        'id' => 0,
        'name' => 'NOT_READ'
    ];
    const READ = [
        'id' => 1,
        'name' => 'READ'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::NOT_READ,
            self::READ
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getNotRead()
    {
        return self::NOT_READ['id'];
    }
    public static function getRead()
    {
        return self::READ['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::NOT_READ['name'] => self::NOT_READ['id'],
            self::READ['name'] => self::READ['id']
        ];
    }
}

<?php

namespace App\Enums\Types;

class ContactStatus
{
    //Declare Name And Value
    const NOT_ACTIVATE = [
        'id' => 0,
        'name' => 'NOT_ACTIVATE'
    ];
    const ACTIVATED = [
        'id' => 1,
        'name' => 'ACTIVATED'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::NOT_ACTIVATE,
            self::ACTIVATED
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getNotActivate()
    {
        return self::NOT_ACTIVATE['id'];
    }
    public static function getActivated()
    {
        return self::ACTIVATED['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::NOT_ACTIVATE['name'] => self::NOT_ACTIVATE['id'],
            self::ACTIVATED['name'] => self::ACTIVATED['id']
        ];
    }
}

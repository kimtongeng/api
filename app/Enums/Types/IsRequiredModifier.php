<?php

namespace App\Enums\Types;

class IsRequiredModifier
{
    //Declare Name and Value
    const YES = [
        'id' => 1,
        'name' => 'YES'
    ];
    const NO = [
        'id' => 0,
        'name' => 'NO'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::NO,
            self::YES
        ];
    }

    //Get Value By Function Name (For API)
    public static function getYes()
    {
        return self::YES['id'];
    }

    public static function getNo()
    {
        return self::NO['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::YES['name'] => self::YES['id'],
            self::NO['name'] => self::NO['id']
        ];
    }
}

<?php

namespace App\Enums\Types;

class PropertyAssetActive
{
    //Declare Name And Value
    const FALSE = [
        'id' => 0,
        'name' => 'FALSE'
    ];
    const TRUE = [
        'id' => 1,
        'name' => 'TRUE'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::TRUE,
            self::FALSE
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getFalse()
    {
        return self::FALSE['id'];
    }
    public static function getTrue()
    {
        return self::TRUE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::TRUE['name'] => self::TRUE['id'],
            self::FALSE['name'] => self::FALSE['id']
        ];
    }
}

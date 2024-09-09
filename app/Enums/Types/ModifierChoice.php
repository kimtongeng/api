<?php

namespace App\Enums\Types;

class ModifierChoice
{
    //Declare Name And Value
    const SINGLE = [
        'id' => 1,
        'name' => 'SINGLE'
    ];
    const MULTI = [
        'id' => 2,
        'name' => 'MULTI'
    ];

    //Get Value By Function Name (For Api)
    public static function getSingle()
    {
        return self::SINGLE['id'];
    }

    public static function getMulti()
    {
        return self::MULTI['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::SINGLE['name'] => self::SINGLE['id'],
            self::MULTI['name'] => self::MULTI['id']
        ];
    }
}

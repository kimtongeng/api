<?php

namespace App\Enums\Types;

class BusinessTypeHasTransaction
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

    //Get Value By Function Name (For Api)
    public static function getNo()
    {
        return self::NO['id'];
    }

    public static function getYes()
    {
        return self::YES['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::NO['name'] => self::NO['id'],
            self::YES['name'] => self::YES['id']
        ];
    }
}

<?php

namespace App\Enums\Types;

class AdjustItemQuantityType
{
    const INCREMENT = [
        'id' => 1,
        'name' => 'INCREMENT'
    ];
    const DECREMENT = [
        'id' => 2,
        'name' => 'DECREMENT'
    ];

    //Get Value By Function Name (For Api)
    public static function getIncrement()
    {
        return self::INCREMENT['id'];
    }

    public static function getDecrement()
    {
        return self::DECREMENT['id'];
    }
}

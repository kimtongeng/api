<?php

namespace App\Enums\Types;

class PlacePriceStatus
{
    //Declare Name And Value
    const FOR_SALE = [
        'id' => 1,
        'name' => 'FOR_SALE',
    ];
    const NOT_FOR_SALE = [
        'id' => 2,
        'name' => 'NOT_FOR_SALE',
    ];

    //Get Value By function Name (For api)
    public static function getForSale()
    {
        return self::FOR_SALE['id'];
    }
    public static function getNotForSale()
    {
        return self::NOT_FOR_SALE['id'];
    }
}

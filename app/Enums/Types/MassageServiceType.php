<?php

namespace App\Enums\Types;

class MassageServiceType
{
    const MASSAGE_AT_SHOP = [
        'id' => 1,
        'name' => 'MASSAGE_AT_SHOP',
    ];
    const MASSAGE_TO_THE_PLACE = [
        'id' => 2,
        'name' => 'MASSAGE_TO_THE_PLACE',
    ];

    //Get Value By Function Name (For Api)
    public static function getMassageAtShop()
    {
        return self::MASSAGE_AT_SHOP['id'];
    }
    public static function getMassageToThePlace()
    {
        return self::MASSAGE_TO_THE_PLACE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::MASSAGE_AT_SHOP['name'] => self::MASSAGE_AT_SHOP['id'],
            self::MASSAGE_TO_THE_PLACE['name'] => self::MASSAGE_TO_THE_PLACE['id']
        ];
    }
}

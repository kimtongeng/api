<?php

namespace App\Enums\Types;

class CommissionType
{
    //Declare Name And Value
    const AMOUNT = [
        'id' => 1,
        'name' => 'AMOUNT'
    ];
    const PERCENTAGE = [
        'id' => 2,
        'name' => 'PERCENTAGE'
    ];

    //Get Value By Function Name (For Api)
    public static function getAmount()
    {
        return self::AMOUNT['id'];
    }

    public static function getPercentage()
    {
        return self::PERCENTAGE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::AMOUNT['name'] => self::AMOUNT['id'],
            self::PERCENTAGE['name'] => self::PERCENTAGE['id']
        ];
    }
}

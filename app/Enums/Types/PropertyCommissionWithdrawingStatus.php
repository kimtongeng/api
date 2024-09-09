<?php

namespace App\Enums\Types;

class PropertyCommissionWithdrawingStatus
{
    //Declare Name And Value
    const PENDING = [
        'id' => 0,
        'name' => 'PENDING'
    ];
    const CONFIRMED = [
        'id' => 1,
        'name' => 'CONFIRMED'
    ];
    const REJECTED = [
        'id' => 2,
        'name' => 'REJECTED'
    ];

    //Get Value By Function Name (For Api)
    public static function getPending()
    {
        return self::PENDING['id'];
    }

    public static function getConfirmed()
    {
        return self::CONFIRMED['id'];
    }


    public static function getRejected()
    {
        return self::REJECTED['id'];
    }


    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::PENDING['name'] => self::PENDING['id'],
            self::CONFIRMED['name'] => self::CONFIRMED['id'],
            self::REJECTED['name'] => self::REJECTED['id'],
        ];
    }
}

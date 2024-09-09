<?php

namespace App\Enums\Types;

class RoomStatus {
    const DISABLE = [
        'id' => 0,
        'name' => 'DISABLE'
    ];
    const ENABLE = [
        'id' => 1,
        'name' => 'ENABLE'
    ];
    const BOOKING = [
        'id' => 2,
        'name' => 'BOOKING',
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::ENABLE,
            self::DISABLE,
            self::BOOKING,
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getDisable()
    {
        return self::DISABLE['id'];
    }

    public static function getEnable()
    {
        return self::ENABLE['id'];
    }

    public static function getBooking()
    {
        return self::BOOKING['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::ENABLE['name'] => self::ENABLE['id'],
            self::DISABLE['name'] => self::DISABLE['id'],
            self::BOOKING['name'] => self::BOOKING['id'],
        ];
    }
}

<?php

namespace App\Enums\Types;

class PropertyAssetStatus
{
    //Declare Name And Value
    const NOT_BOOKING = [
        'id' => 0,
        'name' => 'NOT_BOOKING'
    ];
    const BOOKING = [
        'id' => 1,
        'name' => 'BOOKING'
    ];
    const COMPLETED_BOOKING = [
        'id' => 2,
        'name' => 'COMPLETED_BOOKING'
    ];

    //Get Value By Function Name (For Api)

    public static function getNotBooking()
    {
        return self::NOT_BOOKING['id'];
    }
    public static function getBooking()
    {
        return self::BOOKING['id'];
    }

    public static function getCompletedBooking()
    {
        return self::COMPLETED_BOOKING['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::NOT_BOOKING['name'] => self::NOT_BOOKING['id'],
            self::BOOKING['name'] => self::BOOKING['id'],
            self::COMPLETED_BOOKING['name'] => self::COMPLETED_BOOKING['id']
        ];
    }
}

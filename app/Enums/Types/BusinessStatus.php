<?php

namespace App\Enums\Types;

class BusinessStatus
{
    //Declare Name And Value
    const PENDING = [
        'id' => 0,
        'name' => 'PENDING'
    ];
    const APPROVED = [
        'id' => 1,
        'name' => 'APPROVED'
    ];
    const BOOKING = [
        'id' => 2,
        'name' => 'BOOKING'
    ];
    const COMPLETED_BOOKING = [
        'id' => 3,
        'name' => 'COMPLETED_BOOKING'
    ];
    const DISABLE = [
        'id' => 4,
        'name' => 'DISABLE'
    ];

    //Get Value By Function Name (For Api)
    public static function getPending()
    {
        return self::PENDING['id'];
    }

    public static function getApproved()
    {
        return self::APPROVED['id'];
    }


    public static function getBooking()
    {
        return self::BOOKING['id'];
    }

    public static function getCompletedBooking()
    {
        return self::COMPLETED_BOOKING['id'];
    }

    public static function getDisable()
    {
        return self::DISABLE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::PENDING['name'] => self::PENDING['id'],
            self::APPROVED['name'] => self::APPROVED['id'],
            self::BOOKING['name'] => self::BOOKING['id'],
            self::COMPLETED_BOOKING['name'] => self::COMPLETED_BOOKING['id'],
            self::DISABLE['name'] => self::DISABLE['id'],
        ];
    }
}

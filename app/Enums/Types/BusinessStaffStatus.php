<?php

namespace App\Enums\Types;

class BusinessStaffStatus
{
    const DISABLE = [
        'id' => 0,
        'name' => 'DISABLE',
    ];
    const PENDING = [
        'id' => 1,
        'name' => 'PENDING',
    ];
    const ENABLE = [
        'id' => 2,
        'name' => 'ENABLE',
    ];
    const REJECT = [
        'id' => 3,
        'name' => 'REJECT',
    ];
    const BOOKING = [
        'id' => 4,
        'name' => 'BOOKING',
    ];

    //Get Value By Function Name (for API)
    public static function getDisable()
    {
        return self::DISABLE['id'];
    }
    public static function getPending()
    {
        return self::PENDING['id'];
    }
    public static function getEnable()
    {
        return self::ENABLE['id'];
    }
    public static function getReject()
    {
        return self::REJECT['id'];
    }
    public static function getBooking()
    {
        return self::BOOKING['id'];
    }
}

<?php

namespace App\Enums\Types;

class StaffWorkDaysEnums
{
    const MONDAY = [
        'id' => 0,
        'name' => 'MONDAY',
    ];
    const TUESDAY = [
        'id' => 1,
        'name' => 'TUESDAY',
    ];
    const WEDNESDAY = [
        'id' => 2,
        'name' => 'WEDNESDAY',
    ];
    const THURSDAY = [
        'id' => 3,
        'name' => 'THURSDAY',
    ];
    const FRIDAY = [
        'id' => 4,
        'name' => 'FRIDAY',
    ];
    const SATURDAY = [
        'id' => 5,
        'name' => 'SATURDAY',
    ];
    const SUNDAY = [
        'id' => 6,
        'name' => 'SUNDAY',
    ];

    //Get Value Function By Name(for API)
    public static function getSunday()
    {
        return self::SUNDAY['id'];
    }
    public static function getMonday()
    {
        return self::MONDAY['id'];
    }
    public static function getTuesday()
    {
        return self::TUESDAY['id'];
    }
    public static function getWednesday()
    {
        return self::WEDNESDAY['id'];
    }
    public static function getThursday()
    {
        return self::THURSDAY['id'];
    }
    public static function getFriday()
    {
        return self::FRIDAY['id'];
    }
    public static function getSaturday()
    {
        return self ::SATURDAY['id'];
    }
}

<?php

namespace App\Enums\Types;


class ContactBroadcastType
{
    //Declare Name And Value
    const SELF = [
        'id' => 1,
        'name' => 'SELF'
    ];
    const ADVERTISE = [
        'id' => 2,
        'name' => 'ADVERTISE'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::SELF,
            self::ADVERTISE
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getSelf()
    {
        return self::SELF['id'];
    }

    public static function getAdvertise()
    {
        return self::ADVERTISE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::SELF['name'] => self::SELF['id'],
            self::ADVERTISE['name'] => self::ADVERTISE['id']
        ];
    }
}

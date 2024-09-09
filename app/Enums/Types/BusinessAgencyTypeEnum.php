<?php

namespace App\Enums\Types;

class BusinessAgencyTypeEnum
{
    //Declare Name And Value
    const PROPERTY_AGENCY = [
        'id' => 1,
        'name' => 'PROPERTY_AGENCY'
    ];
    const MESSAGE_THERAPIST = [
        'id' => 2,
        'name' => 'MESSAGE_THERAPIST'
    ];
    const KTV = [
        'id' => 3,
        'name' => 'KTV'
    ];
    const DRIVER = [
        'id' => 4,
        'name' => 'DRIVER'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::PROPERTY_AGENCY,
            self::MESSAGE_THERAPIST,
            self::KTV,
            self::DRIVER,
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getPropertyAgency()
    {
        return self::PROPERTY_AGENCY['id'];
    }

    public static function getMessageTherapist()
    {
        return self::MESSAGE_THERAPIST['id'];
    }

    public static function getKtv()
    {
        return self::KTV['id'];
    }

    public static function getDriver()
    {
        return self::DRIVER['id'];
    }


    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::PROPERTY_AGENCY['name'] => self::PROPERTY_AGENCY['id'],
            self::MESSAGE_THERAPIST['name'] => self::MESSAGE_THERAPIST['id'],
            self::KTV['name'] => self::KTV['id'],
            self::DRIVER['name'] => self::DRIVER['id'],
        ];
    }
}

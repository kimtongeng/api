<?php

namespace App\Enums\Types;

class DocumentTypeEnum
{
    const ID_NO = [
        'id' => 1,
        'name' => 'ID_NO'
    ];
    const PASSPORT = [
        'id' => 2,
        'name' => 'PASSPORT'
    ];
    const LAND_TITLE = [
        'id' => 3,
        'name' => 'LAND_TITLE'
    ];
    const BUSINESS_LICENSE = [
        'id' => 4,
        'name' => 'BUSINESS_LICENSE'
    ];

    public static function getIDNo()
    {
        return self::ID_NO['id'];
    }

    public static function getPassport()
    {
        return self::PASSPORT['id'];
    }

    public static function getLandTitle()
    {
        return self::LAND_TITLE['id'];
    }

    public static function getBusinessLicense()
    {
        return self::BUSINESS_LICENSE['id'];
    }
}

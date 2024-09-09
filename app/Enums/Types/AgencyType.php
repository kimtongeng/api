<?php

namespace App\Enums\Types;

class AgencyType
{
    //Declare Name And Value
    const BASE = [
        'id' => 1,
        'name' => 'BASE'
    ];
    const REFERRAL = [
        'id' => 2,
        'name' => 'REFERRAL'
    ];
    const SALE_ASSiSTANCE = [
        'id' => 3,
        'name' => 'SALE_ASSiSTANCE'
    ];

    //Get Value By Function Name (For Api)
    public static function getBase()
    {
        return self::BASE['id'];
    }

    public static function getReferral()
    {
        return self::REFERRAL['id'];
    }
    public static function getSaleAssistance()
    {
        return self::SALE_ASSiSTANCE['id'];
    }
}

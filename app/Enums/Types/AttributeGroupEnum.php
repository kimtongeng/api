<?php

namespace App\Enums\Types;

class AttributeGroupEnum
{
    const FACILITIES = [
        'id' => 1,
        'name' => 'FACILITIES',
    ];

    public static function getFacilities()
    {
        return self::FACILITIES['id'];
    }
}

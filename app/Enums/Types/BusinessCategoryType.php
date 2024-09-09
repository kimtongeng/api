<?php

namespace App\Enums\Types;

class BusinessCategoryType
{
    //Declare Name And Value
    const EVENT_TYPE = [
        'id' => 1,
        'name' => 'EVENT_TYPE',
    ];
    const POSITION_GROUP = [
        'id' => 2,
        'name' => 'POSITION_GROUP',
    ];

    //Get Value By Function Name (For Api)
    public static function getEventType()
    {
        return self::EVENT_TYPE['id'];
    }

    public static function getPositionGroup()
    {
        return self::POSITION_GROUP['id'];
    }
}

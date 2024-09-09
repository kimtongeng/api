<?php

namespace App\Enums\Types;

class NewsVisitorsStatus
{
    //Declare Name And Value
    const PENDING = [
        'id' => 0,
        'name' => 'PENDING',
    ];
    const JOIN = [
        'id' => 1,
        'name' => 'JOIN',
    ];
    const LEAVE = [
        'id' => 2,
        'name' => 'LEAVE',
    ];

    // Get Value By Function Name For (api)
    public static function getPending()
    {
        return self::PENDING['id'];
    }
    public static function getJoin()
    {
        return self::JOIN['id'];
    }
    public static function getLeave()
    {
        return self::LEAVE['id'];
    }
}

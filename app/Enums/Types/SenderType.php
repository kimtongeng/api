<?php

namespace App\Enums\Types;

class SenderType
{
    //Declare Name And Value
    const POSTER = [
        'id' => 1,
        'name' => 'POSTER',
    ];
    const PARTICIPANT = [
        'id' => 2,
        'name' => 'PARTICIPANT',
    ];

    //Get Value By Function Name (For Api)
    public static function getPosterSender()
    {
        return self::POSTER['id'];
    }
    public static function getParticipantSender()
    {
        return self::PARTICIPANT['id'];
    }
}

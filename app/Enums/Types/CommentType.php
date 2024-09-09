<?php

namespace App\Enums\Types;

class CommentType
{
    //Declare Name And Value
    const TEXT = [
        'id' => 1,
        'name' => 'TEXT',
    ];
    const IMAGE = [
        'id' => 2,
        'name' => 'IMAGE',
    ];
    const AUDIO = [
        'id' => 3,
        'name' => 'AUDIO',
    ];

    //Get Value By Function Name (For Api)
    public static function getText()
    {
        return self::TEXT['id'];
    }
    public static function getImage()
    {
        return self::IMAGE['id'];
    }
    public static function getAudio()
    {
        return self::AUDIO['id'];
    }
}

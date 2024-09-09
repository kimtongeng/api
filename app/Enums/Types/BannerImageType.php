<?php

namespace App\Enums\Types;

class BannerImageType
{
    //Declare Name And Value
    const SQUARE = [
        'id' => 1,
        'name' => 'SQUARE'
    ];
    const RECTANGLE = [
        'id' => 2,
        'name' => 'RECTANGLE'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::SQUARE,
            self::RECTANGLE
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getSquare()
    {
        return self::SQUARE['id'];
    }
    public static function getRectangle()
    {
        return self::RECTANGLE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::SQUARE['name'] => self::SQUARE['id'],
            self::RECTANGLE['name'] => self::RECTANGLE['id']
        ];
    }
}

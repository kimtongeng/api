<?php

namespace App\Enums\Types;

class ContactGender
{
    //Declare Name And Value
    const MALE = [
        'id' => 1,
        'name' => 'MALE'
    ];
    const FEMALE = [
        'id' => 2,
        'name' => 'FEMALE'
    ];
    const OTHER = [
        'id' => 3,
        'name' => 'OTHER'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::MALE,
            self::FEMALE,
            self::OTHER
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getMale()
    {
        return self::MALE['id'];
    }
    public static function getFemale()
    {
        return self::FEMALE['id'];
    }
    public static function getOther()
    {
        return self::OTHER['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::MALE['name'] => self::MALE['id'],
            self::FEMALE['name'] => self::FEMALE['id'],
            self::OTHER['name'] => self::OTHER['id'],
        ];
    }
}

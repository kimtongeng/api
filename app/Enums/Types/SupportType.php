<?php

namespace App\Enums\Types;


class SupportType
{
    //Declare Name And Value
    const PHONE_1 = [
        'id' => 1,
        'name' => 'PHONE_1'
    ];
    const PHONE_2 = [
        'id' => 2,
        'name' => 'PHONE_2'
    ];
    const PHONE_3 = [
        'id' => 3,
        'name' => 'PHONE_3'
    ];
    const EMAIL = [
        'id' => 4,
        'name' => 'EMAIL'
    ];
    const FACEBOOK = [
        'id' => 5,
        'name' => 'FACEBOOK'
    ];
    const INSTAGRAM = [
        'id' => 6,
        'name' => 'INSTAGRAM'
    ];
    const TELEGRAM = [
        'id' => 7,
        'name' => 'TELEGRAM'
    ];
    const YOUTUBE = [
        'id' => 8,
        'name' => 'YOUTUBE'
    ];
    const LINE = [
        'id' => 9,
        'name' => 'LINE'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::PHONE_1,
            self::PHONE_2,
            self::PHONE_3,
            self::EMAIL,
            self::FACEBOOK,
            self::INSTAGRAM,
            self::TELEGRAM,
            self::YOUTUBE,
            self::LINE
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getPhone_1()
    {
        return self::PHONE_1['name'];
    }
    public static function getPhone_2()
    {
        return self::PHONE_2['name'];
    }
    public static function getPhone_3()
    {
        return self::PHONE_3['name'];
    }
    public static function getEmail()
    {
        return self::EMAIL['name'];
    }
    public static function getFacebook()
    {
        return self::FACEBOOK['name'];
    }
    public static function getInstagram()
    {
        return self::INSTAGRAM['name'];
    }
    public static function getYoutube()
    {
        return self::YOUTUBE['name'];
    }
    public static function getLine()
    {
        return self::LINE['name'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::PHONE_1['name'] => self::PHONE_1['id'],
            self::PHONE_2['name'] => self::PHONE_2['id'],
            self::PHONE_3['name'] => self::PHONE_3['id'],
            self::EMAIL['name'] => self::EMAIL['id'],
            self::FACEBOOK['name'] => self::FACEBOOK['id'],
            self::INSTAGRAM['name'] => self::INSTAGRAM['id'],
            self::LINE['name'] => self::LINE['id']
        ];
    }
}

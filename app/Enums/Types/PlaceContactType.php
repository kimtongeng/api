<?php

namespace App\Enums\Types;

class PlaceContactType
{
    //Declare Name And Value
    const FACEBOOK = [
        'id' => 1,
        'name' => 'Facebook',
        'image' => 'facebook.svg'
    ];
    const INSTAGRAM = [
        'id' => 2,
        'name' => 'Instagram',
        'image' => 'instagram.svg'
    ];
    const TELEGRAM = [
        'id' => 3,
        'name' => 'Telegram',
        'image' => 'telegram.svg'
    ];
    const YOUTUBE = [
        'id' => 4,
        'name' => 'Youtube',
        'image' => 'youtube.svg'
    ];
    const TWITTER = [
        'id' => 5,
        'name' => 'Twitter',
        'image' => 'twitter.svg'
    ];
    const WHATSAPP = [
        'id' => 6,
        'name' => 'Whatsapp',
        'image' => 'whatsapp.svg'
    ];
    const PHONE = [
        'id' => 7,
        'name' => 'Phone',
        'image' => 'phone.svg'
    ];
    const GMAIL = [
        'id' => 8,
        'name' => 'Gmail',
        'image' => 'gmail.svg'
    ];
    const LINE = [
        'id' => 9,
        'name' => 'Line',
        'image' => 'line.svg'
    ];


    //Get Combo List
    public static function getComboList()
    {
        return [
            self::FACEBOOK,
            self::INSTAGRAM,
            self::TELEGRAM,
            self::YOUTUBE,
            self::TWITTER,
            self::WHATSAPP,
            self::PHONE,
            self::GMAIL,
            self::LINE,
        ];
    }
}

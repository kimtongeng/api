<?php

namespace App\Enums\Types;

class AppTypeEnum
{
    const PROPERTY = [
        'id' => 1,
        'name' => 'PROPERTY'
    ];
    const SHOP = [
        'id' => 2,
        'name' => 'SHOP'
    ];
    const ACCOMMODATION = [
        'id' => 3,
        'name' => 'ACCOMMODATION'
    ];
    const ATTRACTION = [
        'id' => 4,
        'name' => 'ATTRACTION'
    ];
    const DELIVERY = [
        'id' => 5,
        'name' => 'DELIVERY'
    ];
    const NEWS = [
        'id' => 6,
        'name' => 'NEWS'
    ];
    const CHARITY = [
        'id' => 7,
        'name' => 'CHARITY'
    ];
    const MASSAGE = [
        'id' => 8,
        'name' => 'MASSAGE',
    ];
    const KTV = [
        'id' => 9,
        'name' => 'KTV',
    ];

    public static function getProperty()
    {
        return self::PROPERTY['id'];
    }

    public static function getShop()
    {
        return self::SHOP['id'];
    }

    public static function getAccommodation()
    {
        return self::ACCOMMODATION['id'];
    }

    public static function getAttraction()
    {
        return self::ATTRACTION['id'];
    }

    public static function getDelivery()
    {
        return self::DELIVERY['id'];
    }

    public static function getNews()
    {
        return self::NEWS['id'];
    }
    public static function getCharity()
    {
        return self::CHARITY['id'];
    }
    public static function getMassage()
    {
        return self::MASSAGE['id'];
    }
    public static function getKtv()
    {
        return self::KTV['id'];
    }
}

<?php

namespace App\Enums\Types;

class GalleryPhotoType
{
    const PROPERTY = [
        'id' => 1,
        'name' => 'PROPERTY'
    ];
    const PROPERTY_ASSET = [
        'id' => 2,
        'name' => 'PROPERTY_ASSET'
    ];
    const ATTRACTION = [
        'id' => 3,
        'name' => 'ATTRACTION'
    ];
    const SHOP_COVER = [
        'id' => 4,
        'name' => 'SHOP_COVER'
    ];
    const SHOP_PRODUCT = [
        'id' => 5,
        'name' => 'SHOP_PRODUCT'
    ];
    const CHARITY_ORGANIZATION = [
        'id' => 6,
        'name' => 'CHARITY_ORGANIZATION'
    ];
    const ACCOMMODATION_COVER = [
        'id' => 7,
        'name' => 'ACCOMMODATION_COVER'
    ];
    const ACCOMMODATION_ROOM = [
        'id' => 8,
        'name' => 'ACCOMMODATION_ROOM',
    ];
    const MASSAGE_COVER = [
        'id' => 9,
        'name' => 'MASSAGE_COVER',
    ];
    const MASSAGE_SERVICE = [
        'id' => 10,
        'name' => 'MASSAGE_SERVICE',
    ];
    const NEWS_COVER = [
        'id' => 11,
        'name' => 'NEWS_COVER',
    ];
    const KTV_COVER = [
        'id' => 12,
        'name' => 'KTV_COVER',
    ];
    const KTV_ROOM_COVER = [
        'id' => 13,
        'name' => 'KTV_ROOM_COVER',
    ];

    public static function getProperty()
    {
        return self::PROPERTY['id'];
    }

    public static function getPropertyAsset()
    {
        return self::PROPERTY_ASSET['id'];
    }

    public static function getAttraction()
    {
        return self::ATTRACTION['id'];
    }
    public static function getShopCover()
    {
        return self::SHOP_COVER['id'];
    }
    public static function getShopProduct()
    {
        return self::SHOP_PRODUCT['id'];
    }
    public static function getCharityOrganization()
    {
        return self::CHARITY_ORGANIZATION['id'];
    }
    public static function getAccommodationCover()
    {
        return self::ACCOMMODATION_COVER['id'];
    }
    public static function getAccommodationRoom()
    {
        return self::ACCOMMODATION_ROOM['id'];
    }
    public static function getMassageCover()
    {
        return self::MASSAGE_COVER['id'];
    }
    public static function getMassagerService()
    {
        return self::MASSAGE_SERVICE['id'];
    }
    public static function getNewsCover()
    {
        return self::NEWS_COVER['id'];
    }
    public static function getKtvCover()
    {
        return self::KTV_COVER['id'];
    }
    public static function getKtvRoomCover()
    {
        return self::KTV_ROOM_COVER['id'];
    }
}

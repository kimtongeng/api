<?php

namespace App\Enums\Types;

class BusinessTypeEnum
{
    //Declare Name And Value
    const PROPERTY = [
        'id' => 1,
        'name' => 'PROPERTY'
    ];
    const ACCOMMODATION = [
        'id' => 2,
        'name' => 'ACCOMMODATION'
    ];
    const DELIVERY = [
        'id' => 3,
        'name' => 'DELIVERY'
    ];
    const SHOP_RETAIL = [
        'id' => 4,
        'name' => 'SHOP_RETAIL'
    ];
    const SHOP_WHOLESALE = [
        'id' => 5,
        'name' => 'SHOP_WHOLESALE'
    ];
    const RESTAURANT = [
        'id' => 6,
        'name' => 'RESTAURANT'
    ];
    const ATTRACTION = [
        'id' => 7,
        'name' => 'ATTRACTION'
    ];
    const NEWS = [
        'id' => 8,
        'name' => 'NEWS'
    ];
    const SHOP_LOCAL_PRODUCT = [
        'id' => 9,
        'name' => 'SHOP_LOCAL_PRODUCT'
    ];
    const CHARITY_ORGANIZATION = [
        'id' => 10,
        'name' => 'CHARITY_ORGANIZATION'
    ];
    const MASSAGE = [
        'id' => 11,
        'name' => 'MASSAGE',
    ];
    const SERVICE = [
        'id' => 12,
        'name' => 'SERVICE',
    ];
    const KTV = [
        'id' => 13,
        'name' => 'KTV',
    ];
    const MODERN_COMMUNITY = [
        'id' => 14,
        'name' => 'MODERN_COMMUNITY',
    ];
    const CARRIER = [
        'id' => 15,
        'name' => 'CARRIER',
    ];
    const DISTRIBUTOR = [
        'id' => 16,
        'name' => 'DISTRIBUTOR',
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::PROPERTY,
            self::ACCOMMODATION,
            self::DELIVERY,
            self::SHOP_RETAIL,
            self::SHOP_WHOLESALE,
            self::RESTAURANT,
            self::ATTRACTION,
            self::NEWS,
            self::SHOP_LOCAL_PRODUCT,
            self::CHARITY_ORGANIZATION,
            self::MASSAGE,
            self::SERVICE,
            self::KTV,
            self::MODERN_COMMUNITY,
            self::CARRIER,
            self::DISTRIBUTOR,
        ];
    }

    // Get Combo List Shop
    public static function getComboListShop()
    {
        return [
            self::SHOP_RETAIL,
            self::SHOP_WHOLESALE,
            self::RESTAURANT,
            self::SHOP_LOCAL_PRODUCT,
            self::SERVICE,
            self::MODERN_COMMUNITY,
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getProperty()
    {
        return self::PROPERTY['id'];
    }

    public static function getAccommodation()
    {
        return self::ACCOMMODATION['id'];
    }

    public static function getDelivery()
    {
        return self::DELIVERY['id'];
    }

    public static function getShopRetail()
    {
        return self::SHOP_RETAIL['id'];
    }

    public static function getShopWholesale()
    {
        return self::SHOP_WHOLESALE['id'];
    }

    public static function getRestaurant()
    {
        return self::RESTAURANT['id'];
    }

    public static function getAttraction()
    {
        return self::ATTRACTION['id'];
    }

    public static function getNews()
    {
        return self::NEWS['id'];
    }
    public static function getShopLocalProduct()
    {
        return self::SHOP_LOCAL_PRODUCT['id'];
    }
    public static function getCharityOrganization()
    {
        return self::CHARITY_ORGANIZATION['id'];
    }
    public static function getMassage()
    {
        return self::MASSAGE['id'];
    }
    public static function getService()
    {
        return self::SERVICE['id'];
    }
    public static function getKtv()
    {
        return self::KTV['id'];
    }
    public static function getModernCommunity()
    {
        return self::MODERN_COMMUNITY['id'];
    }
    public static function getCarrier()
    {
        return self::CARRIER['id'];
    }
    public static function getDistributor()
    {
        return self::DISTRIBUTOR['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::PROPERTY['name'] => self::PROPERTY['id'],
            self::ACCOMMODATION['name'] => self::ACCOMMODATION['id'],
            self::DELIVERY['name'] => self::DELIVERY['id'],
            self::SHOP_RETAIL['name'] => self::SHOP_RETAIL['id'],
            self::SHOP_WHOLESALE['name'] => self::SHOP_WHOLESALE['id'],
            self::RESTAURANT['name'] => self::RESTAURANT['id'],
            self::ATTRACTION['name'] => self::ATTRACTION['id'],
            self::NEWS['name'] => self::NEWS['id'],
            self::SHOP_LOCAL_PRODUCT['name'] => self::SHOP_LOCAL_PRODUCT['id'],
            self::CHARITY_ORGANIZATION['name'] => self::CHARITY_ORGANIZATION['id'],
            self::MASSAGE['name'] => self::MASSAGE['id'],
            self::SERVICE['name'] => self::SERVICE['id'],
            self::KTV['name'] => self::KTV['id'],
            self::MODERN_COMMUNITY['name'] => self::MODERN_COMMUNITY['id'],
            self::CARRIER['name'] => self::CARRIER['id'],
            self::DISTRIBUTOR['name'] => self::DISTRIBUTOR['id'],
        ];
    }
}

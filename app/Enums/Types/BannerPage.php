<?php

namespace App\Enums\Types;

class BannerPage
{
    //Declare Name And Value
    const HOME = [
        'id' => 1,
        'name' => 'home'
    ];
    const REAL_ESTATE_HOME = [
        'id' => 2,
        'name' => 'real_estate_home'
    ];
    const REAL_ESTATE_BY_PROPERTY_TYPE = [
        'id' => 3,
        'name' => 'real_estate_by_property_type'
    ];
    const ATTRACTION_HOME = [
        'id' => 4,
        'name' => 'attraction_home'
    ];
    const SHOP_RETAIL_HOME = [
        'id' => 5,
        'name' => 'shop_retail_home'
    ];
    const SHOP_WHOLESALE_HOME = [
        'id' => 6,
        'name' => 'shop_wholesale_home'
    ];
    const RESTAURANT_HOME = [
        'id' => 7,
        'name' => 'restaurant_home'
    ];
    const SHOP_LOCAL_PRODUCT_HOME = [
        'id' => 8,
        'name' => 'shop_local_product_home'
    ];
    const HOTEL_HOME = [
        'id' => 9,
        'name' => 'hotel_home'
    ];
    const MASSAGE_HOME = [
        'id' => 10,
        'name' => 'massage_home'
    ];
    const KTV_HOME = [
        'id' => 11,
        'name' => 'ktv_home',
    ];
    const SERVICE = [
        'id' => 12,
        'name' => 'service',
    ];
    const MODERN_COMMUNITY = [
        'id' => 13,
        'name' => 'modern_community',
    ];
    const CHARITY_HOME = [
        'id' => 14,
        'name' => 'charity_home',
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            PositionPlatformType::MOBILE['name'] => [
                self::HOME,
                self::REAL_ESTATE_HOME,
                self::REAL_ESTATE_BY_PROPERTY_TYPE,
                self::ATTRACTION_HOME,
                self::SHOP_RETAIL_HOME,
                self::SHOP_WHOLESALE_HOME,
                self::RESTAURANT_HOME,
                self::SHOP_LOCAL_PRODUCT_HOME,
                self::HOTEL_HOME,
                self::MASSAGE_HOME,
                self::KTV_HOME,
                self::SERVICE,
                self::MODERN_COMMUNITY,
                self::CHARITY_HOME,
            ],
            PositionPlatformType::WEB['name'] => [
            ]
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getHomePage()
    {
        return self::HOME['id'];
    }

    public static function getRealEstateHomePage()
    {
        return self::REAL_ESTATE_HOME['id'];
    }

    public static function getRealEstateByPropertyType()
    {
        return self::REAL_ESTATE_BY_PROPERTY_TYPE['id'];
    }

    public static function getAttractionHome()
    {
        return self::ATTRACTION_HOME['id'];
    }
    public static function getShopRetailHome()
    {
        return self::SHOP_RETAIL_HOME['id'];
    }
    public static function getShopWholesaleHome()
    {
        return self::SHOP_WHOLESALE_HOME['id'];
    }
    public static function getRestaurantHome()
    {
        return self::RESTAURANT_HOME['id'];
    }
    public static function getShopLocalProduct()
    {
        return self::SHOP_LOCAL_PRODUCT_HOME['id'];
    }
    public static function getHotelHome()
    {
        return self::HOTEL_HOME['id'];
    }
    public static function getMassageHome()
    {
        return self::MASSAGE_HOME['id'];
    }
    public static function getKTVHome()
    {
        return self::KTV_HOME['id'];
    }
    public static function getService()
    {
        return self::SERVICE['id'];
    }
    public static function getModernCommunity()
    {
        return self::MODERN_COMMUNITY['id'];
    }
    public static function getCharityHome()
    {
        return self::CHARITY_HOME['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            strtoupper(self::HOME['name']) => self::HOME['id'],
            strtoupper(self::REAL_ESTATE_HOME['name']) => self::REAL_ESTATE_HOME['id'],
            strtoupper(self::REAL_ESTATE_BY_PROPERTY_TYPE['name']) => self::REAL_ESTATE_BY_PROPERTY_TYPE['id'],
            strtoupper(self::ATTRACTION_HOME['name']) => self::ATTRACTION_HOME['id'],
            strtoupper(self::SHOP_RETAIL_HOME['name']) => self::SHOP_RETAIL_HOME['id'],
            strtoupper(self::SHOP_WHOLESALE_HOME['name']) => self::SHOP_WHOLESALE_HOME['id'],
            strtoupper(self::RESTAURANT_HOME['name']) => self::RESTAURANT_HOME['id'],
            strtoupper(self::SHOP_LOCAL_PRODUCT_HOME['name']) => self::SHOP_LOCAL_PRODUCT_HOME['id'],
            strtoupper(self::HOTEL_HOME['name']) => self::HOTEL_HOME['id'],
            strtoupper(self::MASSAGE_HOME['name']) => self::MASSAGE_HOME['id'],
            strtoupper(self::KTV_HOME['name']) => self::KTV_HOME['id'],
            strtoupper(self::SERVICE['name']) => self::SERVICE['id'],
            strtoupper(self::MODERN_COMMUNITY['name']) => self::MODERN_COMMUNITY['id'],
            strtoupper(self::CHARITY_HOME['name']) => self::CHARITY_HOME['id'],
        ];
    }
}

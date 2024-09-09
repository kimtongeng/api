<?php

namespace App\Enums\Types;

class BannerType
{
    //Declare Name And Value
    const LINK = [
        'id' => 1,
        'name' => 'link'
    ];
    const DETAIL = [
        'id' => 2,
        'name' => 'detail'
    ];
    const PROPERTY = [
        'id' => 3,
        'name' => 'property'
    ];
    const PROPERTY_DETAIL = [
        'id' => 4,
        'name' => 'property_detail'
    ];
    const PROPERTY_TYPE = [
        'id' => 5,
        'name' => 'property_type'
    ];
    const ATTRACTION = [
        'id' => 6,
        'name' => 'attraction'
    ];
    const ATTRACTION_DETAIL = [
        'id' => 7,
        'name' => 'attraction_detail'
    ];
    const ATTRACTION_DESTINATION = [
        'id' => 8,
        'name' => 'attraction_destination'
    ];
    const AGENCY_REGISTER = [
        'id' => 9,
        'name' => 'agency_register'
    ];
    const SALE_PROPERTY = [
        'id' => 10,
        'name' => 'sale_property'
    ];
    const SHOP = [
        'id' => 11,
        'name' => 'shop'
    ];
    const CATEGORY_IN_SHOP = [
        'id' => 12,
        'name' => 'category_in_shop'
    ];
    const POPULAR_SHOP = [
        'id' => 13,
        'name' => 'popular_shop'
    ];
    const NEWEST_SHOP = [
        'id' => 15,
        'name' => 'newest_shop'
    ];
    const TOP_RATED_SHOP = [
        'id' => 16,
        'name' => 'top_rated_shop'
    ];
    const FREE_DELIVERY_SHOP = [
        'id' => 17,
        'name' => 'free_delivery_shop'
    ];
    const ALL_SHOP = [
        'id' => 18,
        'name' => 'all_shop'
    ];
    const HOTEL = [
        'id' => 19,
        'name' => 'hotel',
    ];
    const HOTEL_DETAIL = [
        'id' => 20,
        'name' => 'hotel_detail',
    ];
    const MASSAGE = [
        'id' => 21,
        'name' => 'massage',
    ];
    const MASSAGE_DETAIL = [
        'id' => 22,
        'name' => 'massage_detail',
    ];
    const KTV = [
        'id' => 23,
        'name' => 'ktv_place',
    ];
    const KTV_DETAIL = [
        'id' => 24,
        'name' => 'ktv_detail',
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::LINK,
            self::DETAIL,
            self::PROPERTY,
            self::PROPERTY_DETAIL,
            self::PROPERTY_TYPE,
            self::ATTRACTION,
            self::ATTRACTION_DETAIL,
            self::ATTRACTION_DESTINATION,
            self::AGENCY_REGISTER,
            self::SALE_PROPERTY,
            self::SHOP,
            self::CATEGORY_IN_SHOP,
            self::POPULAR_SHOP,
            self::NEWEST_SHOP,
            self::TOP_RATED_SHOP,
            self::FREE_DELIVERY_SHOP,
            self::ALL_SHOP,
            self::HOTEL,
            self::HOTEL_DETAIL,
            self::MASSAGE,
            self::MASSAGE_DETAIL,
            self::KTV,
            self::KTV_DETAIL
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getLink()
    {
        return self::LINK['id'];
    }

    public static function getDetail()
    {
        return self::DETAIL['id'];
    }

    public static function getProperty()
    {
        return self::PROPERTY['id'];
    }

    public static function getPropertyDetail()
    {
        return self::PROPERTY_DETAIL['id'];
    }

    public static function getPropertyType()
    {
        return self::PROPERTY_TYPE['id'];
    }

    public static function getAttraction()
    {
        return self::ATTRACTION['id'];
    }
    public static function getAttractionDetail()
    {
        return self::ATTRACTION_DETAIL['id'];
    }
    public static function getAttractionDestination()
    {
        return self::ATTRACTION_DESTINATION['id'];
    }
    public static function getAgencyRegister()
    {
        return self::AGENCY_REGISTER['id'];
    }
    public static function getSaleProperty()
    {
        return self::SALE_PROPERTY['id'];
    }
    public static function getShop()
    {
        return self::SHOP['id'];
    }
    public static function getCategoryInShop()
    {
        return self::CATEGORY_IN_SHOP['id'];
    }
    public static function getPopularShop()
    {
        return self::POPULAR_SHOP['id'];
    }
    public static function getNewestShop()
    {
        return self::NEWEST_SHOP['id'];
    }
    public static function getTopRated()
    {
        return self::TOP_RATED_SHOP['id'];
    }
    public static  function getFreeDeliveryShop()
    {
        return self::FREE_DELIVERY_SHOP['id'];
    }
    public static function getAllShop()
    {
        return self::ALL_SHOP['id'];
    }
    public static function getHotel()
    {
        return self::HOTEL['id'];
    }
    public static function getHotelDetail()
    {
        return self::HOTEL_DETAIL['id'];
    }
    public static function getMassage()
    {
        return self::MASSAGE['id'];
    }
    public static function getMassageDetail()
    {
        return self::MASSAGE_DETAIL['id'];
    }
    public static function getKTV()
    {
        return self::KTV['id'];
    }
    public static function getKTVDetail()
    {
        return self::KTV_DETAIL['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            strtoupper(self::LINK['name']) => self::LINK['id'],
            strtoupper(self::DETAIL['name']) => self::DETAIL['id'],
            strtoupper(self::PROPERTY['name']) => self::PROPERTY['id'],
            strtoupper(self::PROPERTY_DETAIL['name']) => self::PROPERTY_DETAIL['id'],
            strtoupper(self::PROPERTY_TYPE['name']) => self::PROPERTY_TYPE['id'],
            strtoupper(self::ATTRACTION['name']) => self::ATTRACTION['id'],
            strtoupper(self::ATTRACTION_DETAIL['name']) => self::ATTRACTION_DETAIL['id'],
            strtoupper(self::ATTRACTION_DESTINATION['name']) => self::ATTRACTION_DESTINATION['id'],
            strtoupper(self::AGENCY_REGISTER['name']) => self::AGENCY_REGISTER['id'],
            strtoupper(self::SALE_PROPERTY['name']) => self::SALE_PROPERTY['id'],
            strtoupper(self::SHOP['name']) => self::SHOP['id'],
            strtoupper(self::CATEGORY_IN_SHOP['name']) => self::CATEGORY_IN_SHOP['id'],
            strtoupper(self::POPULAR_SHOP['name']) => self::POPULAR_SHOP['id'],
            strtoupper(self::NEWEST_SHOP['name']) => self::NEWEST_SHOP['id'],
            strtoupper(self::TOP_RATED_SHOP['name']) => self::TOP_RATED_SHOP['id'],
            strtoupper(self::FREE_DELIVERY_SHOP['name']) => self::FREE_DELIVERY_SHOP['id'],
            strtoupper(self::ALL_SHOP['name']) => self::ALL_SHOP['id'],
            strtoupper(self::HOTEL['name']) => self::HOTEL['id'],
            strtoupper(self::HOTEL_DETAIL['name']) => self::HOTEL_DETAIL['id'],
            strtoupper(self::MASSAGE['name']) => self::MASSAGE['id'],
            strtoupper(self::MASSAGE_DETAIL['name']) => self::MASSAGE_DETAIL['id'],
            strtoupper(self::KTV['name']) => self::KTV['id'],
            strtoupper(self::KTV_DETAIL['name']) => self::KTV_DETAIL['id'],
        ];
    }
}

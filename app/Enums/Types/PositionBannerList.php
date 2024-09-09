<?php

namespace App\Enums\Types;

use App\Models\Banner;

class PositionBannerList
{
    //Get Combo List
    public static function getComboList()
    {
        return
            [
                PositionPlatformType::MOBILE['name'] => [
                    strtoupper(BannerPage::HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',
                        ],
                    ],
                    strtoupper(BannerPage::REAL_ESTATE_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ],
                    ],
                    strtoupper(BannerPage::REAL_ESTATE_BY_PROPERTY_TYPE['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ],
                    ],
                    strtoupper(BannerPage::ATTRACTION_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ]
                    ],
                    strtoupper(BannerPage::SHOP_RETAIL_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ],
                        [
                            'text' => 'below_shop_category',
                            'value' => 'below_shop_category',

                        ],
                    ],
                    strtoupper(BannerPage::SHOP_WHOLESALE_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ],
                        [
                            'text' => 'below_shop_category',
                            'value' => 'below_shop_category',

                        ],
                    ],
                    strtoupper(BannerPage::RESTAURANT_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ],
                        [
                            'text' => 'below_shop_category',
                            'value' => 'below_shop_category',

                        ],
                    ],
                    strtoupper(BannerPage::SHOP_LOCAL_PRODUCT_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ],
                        [
                            'text' => 'below_shop_category',
                            'value' => 'below_shop_category',

                        ],
                    ],
                    strtoupper(BannerPage::HOTEL_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',

                        ],
                    ],
                    strtoupper(BannerPage::MASSAGE_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',
                        ]
                    ],
                    strtoupper(BannerPage::KTV_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',
                        ]
                    ],
                    strtoupper(BannerPage::SERVICE['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',
                        ],
                        [
                            'text' => 'below_shop_category',
                            'value' => 'below_shop_category',
                        ],
                    ],
                    strtoupper(BannerPage::MODERN_COMMUNITY['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',
                        ],
                        [
                            'text' => 'below_shop_category',
                            'value' => 'below_shop_category',

                        ],
                    ],
                    strtoupper(BannerPage::CHARITY_HOME['name']) => [
                        [
                            'text' => 'slideshow',
                            'value' => 'slideshow',
                        ],
                    ],
                ],
                PositionPlatformType::WEB['name'] => []
            ];
    }
}

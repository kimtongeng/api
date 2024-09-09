<?php

namespace App\Enums\Types;

class PositionVideoList
{
    //Get Combo List
    public static function getComboList()
    {
        return
            [
                PositionPlatformType::MOBILE['name'] => [
                strtoupper(BannerPage::HOME['name']) => [
                        [
                            'id' => 1,
                            'text' => 'home',
                            'value' => 'home'

                        ],
                    ],
                ],
                PositionPlatformType::WEB['name'] => []
            ];
    }
}

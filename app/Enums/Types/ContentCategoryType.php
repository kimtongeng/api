<?php

namespace App\Enums\Types;

class ContentCategoryType
{
    //Declare Name And Value
    const MENU = [
        'id' => 1,
        'name' => 'MENU'
    ];
    const PAGE_CONTENT = [
        'id' => 2,
        'name' => 'PAGE_CONTENT'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::MENU,
            self::PAGE_CONTENT,
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getMenu()
    {
        return self::MENU['id'];
    }

    public static function getPageContent()
    {
        return self::PAGE_CONTENT['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::MENU['name'] => self::MENU['id'],
            self::PAGE_CONTENT['name'] => self::PAGE_CONTENT['id'],
        ];
    }
}

<?php

namespace App\Enums\Types;

class ChatType
{
    //Declare Name And Value
    const CUSTOMER = [
        'id' => 1,
        'name' => 'CUSTOMER'
    ];
    const ADMIN = [
        'id' => 2,
        'name' => 'ADMIN'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::CUSTOMER,
            self::ADMIN
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getCustomer()
    {
        return self::CUSTOMER['id'];
    }
    public static function getAdmin()
    {
        return self::ADMIN['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::CUSTOMER['name'] => self::CUSTOMER['id'],
            self::ADMIN['name'] => self::ADMIN['id']
        ];
    }
}

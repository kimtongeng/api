<?php

namespace App\Enums\Types;

class TransactionFeeStatus
{
    //Declare Name And Value
    const BUSINESS_NOT_YET_PAY = [
        'id' => 0,
        'name' => 'not_paid',
    ];
    const BUSINESS_PAID = [
        'id' => 1,
        'name' => 'paid',
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::BUSINESS_NOT_YET_PAY,
            self::BUSINESS_PAID
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getBusinessNotYetPay()
    {
        return self::BUSINESS_NOT_YET_PAY['id'];
    }

    public static function getBusinessPaid()
    {
        return self::BUSINESS_PAID['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            strtoupper(self::BUSINESS_NOT_YET_PAY['name']) => self::BUSINESS_NOT_YET_PAY['id'],
            strtoupper(self::BUSINESS_PAID['name']) => self::BUSINESS_PAID['id'],
        ];
    }
}

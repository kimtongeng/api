<?php

namespace App\Enums\Types;

class GeneralSettingKey
{
    //Declare Name And Value
    const WATER_MARK = [
        'id' => 1,
        'name' => 'WATER_MARK'
    ];
    const PROPERTY_TRANSACTION_FEE = [
        'id' => 2,
        'name' => 'PROPERTY_TRANSACTION_FEE'
    ];
    const TRANSACTION_PAYMENT_DEADLINE = [
        'id' => 3,
        'name' => 'TRANSACTION_PAYMENT_DEADLINE'
    ];
    const SECURITY_CODE = [
        'id' => 4,
        'name' => 'SECURITY_CODE'
    ];
    const API_VERSION = [
        'id' => 5,
        'name' => 'API_VERSION'
    ];

    //Get Combo List
    public static function getComboList()
    {
        return [
            self::WATER_MARK,
            self::PROPERTY_TRANSACTION_FEE,
            self::TRANSACTION_PAYMENT_DEADLINE,
            self::SECURITY_CODE,
            self::API_VERSION,
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getWaterMark()
    {
        return self::WATER_MARK['name'];
    }

    public static function getPropertyTransactionFee()
    {
        return self::PROPERTY_TRANSACTION_FEE['name'];
    }

    public static function getTransactionPaymentDeadline()
    {
        return self::TRANSACTION_PAYMENT_DEADLINE['name'];
    }

    public static function getSecurityCode()
    {
        return self::SECURITY_CODE['name'];
    }

    public static function getAPIVersion()
    {
        return self::API_VERSION['name'];
    }


    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::WATER_MARK['name'] => self::WATER_MARK['name'],
            self::PROPERTY_TRANSACTION_FEE['name'] => self::PROPERTY_TRANSACTION_FEE['name'],
            self::TRANSACTION_PAYMENT_DEADLINE['name'] => self::TRANSACTION_PAYMENT_DEADLINE['name'],
            self::SECURITY_CODE['name'] => self::SECURITY_CODE['name'],
            self::API_VERSION['name'] => self::API_VERSION['name'],
        ];
    }
}

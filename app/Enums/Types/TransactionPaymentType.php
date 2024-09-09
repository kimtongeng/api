<?php

namespace App\Enums\Types;

class TransactionPaymentType
{
    //Declare Name and Value
    const CASH_ON_DELIVERY = [
        'id' => 1,
        'name' => 'CASH_ON_DELIVERY',
    ];
    const ONLINE_PAYMENT = [
        'id' => 2,
        'name' => 'ONLINE_PAYMENT',
    ];

    //Get Value By Function Name (For api)
    public static function getCashOnDelivery()
    {
        return self::CASH_ON_DELIVERY['id'];
    }
    public static function getOnlinePayment()
    {
        return self::ONLINE_PAYMENT['id'];
    }
}

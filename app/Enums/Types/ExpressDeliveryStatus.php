<?php

namespace App\Enums\Types;

class ExpressDeliveryStatus
{
    //Declare Name And Value
    const PENDING = [
        'id' => 1,
        'name' => 'PENDING'
    ];
    const DRIVER_ACCEPTED = [
        'id' => 2,
        'name' => 'DRIVER_ACCEPTED'
    ];
    const DRIVER_PICKED_UP = [
        'id' => 3,
        'name' => 'DRIVER_PICKED_UP'
    ];
    const ENROUTE = [
        'id' => 4,
        'name' => 'ENROUTE'
    ];
    const DELIVERED = [
        'id' => 5,
        'name' => 'DELIVERED'
    ];
    const DRIVER_CANCEL_ORDER = [
        'id' => 8,
        'name' => 'DRIVER_CANCEL_ORDER'
    ];
    const CUSTOMER_CANCEL_ORDER = [
        'id' => 9,
        'name' => 'CUSTOMER_CANCEL_ORDER'
    ];

    //Get Value By Function Name (For Api)
    public static function getPending()
    {
        return self::PENDING['id'];
    }

    public static function getDriverAccepted()
    {
        return self::DRIVER_ACCEPTED['id'];
    }

    public static function getDriverPickedUp()
    {
        return self::DRIVER_PICKED_UP['id'];
    }

    public static function getEnroute()
    {
        return self::ENROUTE['id'];
    }

    public static function getDelivered()
    {
        return self::DELIVERED['id'];
    }

    public static function getDriverCancelOrder()
    {
        return self::DRIVER_CANCEL_ORDER['id'];
    }

    public static function getCustomerCancelOrder()
    {
        return self::CUSTOMER_CANCEL_ORDER['id'];
    }
}

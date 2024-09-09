<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryDropLocation extends Model
{
    const TABLE_NAME = 'delivery_drop_location';
    const ID = 'id';
    const DELIVERY_ORDER_ID = 'delivery_order_id';
    const DROP_ORDER_NO = 'drop_order_no';
    const RECIPIENT_LOCATION_LINK = 'recipient_location_link';
    const RECIPIENT_NAME = 'recipient_name';
    const RECIPIENT_PHONE = 'recipient_phone';
    const RECIPIENT_NOTE = 'recipient_note';
    const DURATION = 'duration';
    const DISTANCE = 'distance';
    const PRICE = 'price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::DELIVERY_ORDER_ID} = $data[self::DELIVERY_ORDER_ID];
        $this->{self::DROP_ORDER_NO} = $data[self::DROP_ORDER_NO];
        $this->{self::RECIPIENT_LOCATION_LINK} = $data[self::RECIPIENT_LOCATION_LINK];
        $this->{self::RECIPIENT_NAME} = $data[self::RECIPIENT_NAME];
        $this->{self::RECIPIENT_PHONE} = $data[self::RECIPIENT_PHONE];
        isset($data[self::RECIPIENT_NOTE]) && $this->{self::RECIPIENT_NOTE} = $data[self::RECIPIENT_NOTE];
        $this->{self::DURATION} = $data[self::DURATION];
        $this->{self::DISTANCE} = $data[self::DISTANCE];
        $this->{self::PRICE} = $data[self::PRICE];
    }

}

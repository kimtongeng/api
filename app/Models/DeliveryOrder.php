<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    const TABLE_NAME = 'delivery_order';
    const ID = 'id';
    const ORDER_CODE = 'order_code';
    const CONTACT_ID = 'contact_id';
    const DRIVER_ID = 'driver_id';
    const VEHICLE_TYPE_ID = 'vehicle_type_id';
    const DOCUMENT_TYPE_ID = 'document_type_id';
    const IMAGE = 'image';
    const TRANSACTION_DATE = 'transaction_date';
    const DRIVER_LOCATION_LINK = 'driver_location_link';
    const SENDER_LOCATION_LINK = 'sender_location_link';
    const SENDER_NAME = 'sender_name';
    const SENDER_PHONE = 'sender_phone';
    const SENDER_NOTE = 'sender_note';
    const PAYER = 'payer';
    const PAYMENT_METHOD = 'payment_method';
    const PAYMENT_STATUS = 'payment_status';
    const TOTAL_DURATION = 'total_duration';
    const TOTAL_DISTANCE = 'total_distance';
    const TOTAL_AMOUNT = 'total_amount';
    const CANCEL_REASON = 'cancel_reason';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    /**
     *  Relationship Area
     *
     */
    //Delivery Drop Location
    public function deliveryDropLocation()
    {
        return $this->hasMany(DeliveryDropLocation::class, DeliveryDropLocation::DELIVERY_ORDER_ID, self::ID);
    }


    /**
     * Set Data & List Area
     */
    public function setData($data)
    {
        isset($data[self::ORDER_CODE]) && $this->{self::ORDER_CODE} = $data[self::ORDER_CODE];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        isset($data[self::DRIVER_ID]) && $this->{self::DRIVER_ID} = $data[self::DRIVER_ID];
        $this->{self::VEHICLE_TYPE_ID} = $data[self::VEHICLE_TYPE_ID];
        $this->{self::DOCUMENT_TYPE_ID} = $data[self::DOCUMENT_TYPE_ID];
        $this->{self::TRANSACTION_DATE} = $data[self::TRANSACTION_DATE];
        $this->{self::DRIVER_LOCATION_LINK} = $data[self::DRIVER_LOCATION_LINK];
        $this->{self::SENDER_LOCATION_LINK} = $data[self::SENDER_LOCATION_LINK];
        $this->{self::SENDER_NAME} = $data[self::SENDER_NAME];
        $this->{self::SENDER_PHONE} = $data[self::SENDER_PHONE];
        isset($data[self::SENDER_NOTE]) && $this->{self::SENDER_NOTE} = $data[self::SENDER_NOTE];
        $this->{self::PAYER} = $data[self::PAYER];
        $this->{self::PAYMENT_METHOD} = $data[self::PAYMENT_METHOD];
        $this->{self::PAYMENT_STATUS} = $data[self::PAYMENT_STATUS];
        $this->{self::TOTAL_DURATION} = $data[self::TOTAL_DURATION];
        $this->{self::TOTAL_DISTANCE} = $data[self::TOTAL_DISTANCE];
        isset($data[self::CANCEL_REASON]) && $this->{self::CANCEL_REASON} = $data[self::CANCEL_REASON];
        $this->{self::STATUS} = $data[self::STATUS];
    }


    public static function listDelivery($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('contact', 'contact.id', 'delivery_order.contact_id')
            ->leftjoin('contact as driver', 'driver.id', 'delivery_order.driver_id')
            ->join('vehicle_type', 'vehicle_type.id', 'delivery_order.vehicle_type_id')
            ->select(
                'delivery_drop.id',
                'delivery_drop.order_code',
                'contact.id as contact_id',
                'contact.fullname as contact_name',
            )
            ->with([
                'deliveryDropLocation' => function ($query) {
                    $query->orderBy('delivery_drop_location.drop_order_no', 'asc');
                },
            ])
            ->orderBy('delivery_order.id', 'desc');
    }
}

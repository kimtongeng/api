<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingAddress extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'shipping_address';
    const ID = 'id';
    const CONTACT_ID = 'contact_id';
    const PHONE = 'phone';
    const EMAIL = 'email';
    const ADDRESS = 'address';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';
    const NOTE = 'note';
    const LABEL = 'label';
    const IS_DEFAULT = 'is_default';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;


    //list
    public static function lists()
    {
        return self::select(
            'id',
            'contact_id',
            'phone',
            'email',
            'address',
            'latitude',
            'longitude',
            'note',
            'label',
            'is_default'
        );
    }

    //set data
    public function setData($data)
    {
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::PHONE} = $data[self::PHONE];
        isset($data[self::EMAIL]) && $this->{self::EMAIL} = $data[self::EMAIL];
        $this->{self::ADDRESS} = $data[self::ADDRESS];
        $this->{self::NOTE} = $data[self::NOTE];
        $this->{self::LABEL} = $data[self::LABEL];
        $this->{self::LATITUDE} = $data[self::LATITUDE];
        $this->{self::LONGITUDE} = $data[self::LONGITUDE];
        $this->{self::IS_DEFAULT} = $data[self::IS_DEFAULT];
    }
}

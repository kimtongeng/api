<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    const TABLE_NAME = 'rating';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const ITEM_ID = 'item_id';
    const RATE = 'rate';
    const COMMENT = 'comment';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
       $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
       $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
       $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
       $this->{self::RATE} = $data[self::RATE];
        isset($data[self::COMMENT]) && $this->{self::COMMENT} = $data[self::COMMENT];
    }
}

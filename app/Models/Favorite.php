<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    const TABLE_NAME = 'favorite';
    const ID = 'id';
    const CONTACT_ID = 'contact_id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const BUSINESS_ID = 'business_id';
    const BUSINESS_ITEM_ID = 'business_item_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        isset($data[self::BUSINESS_ITEM_ID]) && $this->{self::BUSINESS_ITEM_ID} = $data[self::BUSINESS_ITEM_ID];
    }

}

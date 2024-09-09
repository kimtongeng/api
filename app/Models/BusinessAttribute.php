<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAttribute extends Model
{
    const TABLE_NAME = 'business_attribute';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const ATTRIBUTE_ID = 'attribute_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::ATTRIBUTE_ID} = $data[self::ATTRIBUTE_ID];
    }
}

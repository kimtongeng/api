<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactBusinessInfo extends Model
{
    const TABLE_NAME = 'contact_business_info';
    const ID = 'id';
    const CONTACT_ID = 'contact_id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const PHONE = 'phone';
    const IMAGE = 'image';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::PHONE} = $data[self::PHONE];
    }
}

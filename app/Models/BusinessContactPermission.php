<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessContactPermission extends Model
{

    const TABLE_NAME = 'business_contact_permission';
    const ID = 'id';
    const BUSINESS_SHARE_CONTACT_ID = 'business_share_contact_id';
    const BUSINESS_PERMISSION_ID = 'business_permission_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_SHARE_CONTACT_ID} = $data[self::BUSINESS_SHARE_CONTACT_ID];
        $this->{self::BUSINESS_PERMISSION_ID} = $data[self::BUSINESS_PERMISSION_ID];
    }

}

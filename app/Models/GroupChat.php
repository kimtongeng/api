<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    const TABLE_NAME = 'group_chat';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const NOTIFICATION_KEY_NAME = 'notification_key_name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::NOTIFICATION_KEY_NAME} = $data[self::NOTIFICATION_KEY_NAME];
    }
}

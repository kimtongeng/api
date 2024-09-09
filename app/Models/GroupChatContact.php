<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChatContact extends Model
{
    const TABLE_NAME = 'group_chat_contact';
    const ID = 'id';
    const GROUP_CHAT_ID = 'group_chat_id';
    const CONTACT_ID = 'contact_id';
    const CONTACT_TYPE = 'contact_type';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::GROUP_CHAT_ID} = $data[self::GROUP_CHAT_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        isset($data[self::CONTACT_TYPE]) && $this->{self::CONTACT_TYPE} = $data[self::CONTACT_TYPE];
    }
}

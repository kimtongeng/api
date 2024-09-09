<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationContact extends Model
{
    const TABLE_NAME = 'notification_contact';
    const ID = 'id';
    const NOTIFICATION_ID = 'notification_id';
    const CONTACT_ID = 'contact_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
       $this->{self::NOTIFICATION_ID} = $data[self::NOTIFICATION_ID];
       $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationView extends Model
{

    const TABLE_NAME = 'notification_view';
    const ID = 'id';
    const CONTACT_ID = 'contact_id';
    const CONTACT_TYPE = 'contact_type';
    const NOTIFICATION_ID = 'notification_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::CONTACT_TYPE} = $data[self::CONTACT_TYPE];
        $this->{self::NOTIFICATION_ID} = $data[self::NOTIFICATION_ID];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTypeNews extends Model
{
    const TABLE_NAME = 'event_type_news';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const EVENT_TYPE_ID = 'event_type_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::EVENT_TYPE_ID} = $data[self::EVENT_TYPE_ID];
    }
}

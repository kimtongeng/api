<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionGroupNews extends Model
{
    const TABLE_NAME = 'position_group_news';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const POSITION_GROUP_ID = 'position_group_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::POSITION_GROUP_ID} = $data[self::POSITION_GROUP_ID];
    }
}

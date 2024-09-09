<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    const TABLE_NAME = 'story';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const BUSINESS_ID = 'business_id';
    const OWNER_ID = 'owner_id';
    const TYPE = 'type';
    const FILENAME = 'filename';
    const EXPIRED_AT = 'expired_at';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::OWNER_ID} = $data[self::OWNER_ID];
        $this->{self::TYPE} = $data[self::TYPE];
        $this->{self::FILENAME} = $data[self::FILENAME];
    }


    public static function listsStory($filter = [])
    {
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $ownerID = isset($filter['owner_id']) ? $filter['owner_id'] : null;
    }
}

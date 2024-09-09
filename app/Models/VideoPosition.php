<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoPosition extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'video_position';
    const ID = 'id';
    const POSITION_ID = 'position_id';
    const VIDEO_ID = 'video_id';
    const VIEW_COUNT = 'view_count';
    const ORDER = 'order';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //set data
    public function setData($data)
    {
        $this->{self::POSITION_ID} = $data[self::POSITION_ID];
        $this->{self::VIDEO_ID} = $data[self::VIDEO_ID];
        $this->{self::ORDER} = $data[self::ORDER];
    }
}

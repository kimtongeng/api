<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PlaceVideoList extends Model
{
    const TABLE_NAME = 'place_video_list';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const LINK = 'link';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;


    //set data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::LINK} = $data[self::LINK];
    }

    //Get count video
    public static function getCountVideo()
    {
        $totalVideo = self::select(DB::raw('COUNT(id) as video'))
            ->first();

        return $totalVideo->video;
    }
}

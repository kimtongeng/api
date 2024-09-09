<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictNews extends Model
{
    const TABLE_NAME = 'district_news';
    const ID = 'id';
    const NEWS_ID = 'news_id';
    const DISTRICT_ID = 'district_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::NEWS_ID} = $data[self::NEWS_ID];
        $this->{self::DISTRICT_ID} = $data[self::DISTRICT_ID];
    }
}

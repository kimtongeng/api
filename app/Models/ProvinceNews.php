<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvinceNews extends Model
{
    const TABLE_NAME = 'province_news';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const PROVINCE_ID = 'province_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::PROVINCE_ID} = $data[self::PROVINCE_ID];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessMultiCategory extends Model
{

    const TABLE_NAME = 'business_multi_category';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const BUSINESS_CATEGORY_ID = 'business_category_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::BUSINESS_CATEGORY_ID} = $data[self::BUSINESS_CATEGORY_ID];
    }

}

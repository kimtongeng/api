<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAgencyType extends Model
{

    const TABLE_NAME = 'business_agency_type';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::NAME} = $data[self::NAME];
    }

}

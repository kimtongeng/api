<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceContact extends Model
{
    const TABLE_NAME = 'place_contact';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const TYPE = 'type';
    const VALUE = 'value';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;


    //set data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::TYPE} = $data[self::TYPE];
        $this->{self::VALUE} = $data[self::VALUE];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeGroup extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'attribute_group';
    const ID    = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const NAME = 'name';
    const KEY = 'key';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    public function setData($data) {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::KEY} = $data[self::KEY];
    }

}

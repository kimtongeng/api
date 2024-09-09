<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModifierOption extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'modifier_option';
    const ID = 'id';
    const MODIFIER_ID = 'modifier_id';
    const NAME = 'name';
    const PRICE = 'price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::MODIFIER_ID} = $data[self::MODIFIER_ID];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::PRICE} = $data[self::PRICE];
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOrderModifierOption extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'product_order_modifier_option';
    const ID = 'id';
    const PRODUCT_ORDER_LIST_ID = 'product_order_list_id';
    const PRODUCT_ID = 'product_id';
    const MODIFIER_ID = 'modifier_id';
    const MODIFIER_NAME = 'modifier_name';
    const MODIFIER_OPTION_ID = 'modifier_option_id';
    const MODIFIER_OPTION_NAME = 'modifier_option_name';
    const MODIFIER_OPTION_PRICE = 'modifier_option_price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::PRODUCT_ORDER_LIST_ID} = $data[self::PRODUCT_ORDER_LIST_ID];
        $this->{self::PRODUCT_ID} = $data[self::PRODUCT_ID];
        $this->{self::MODIFIER_ID} = $data[self::MODIFIER_ID];
        $this->{self::MODIFIER_NAME} = $data[self::MODIFIER_NAME];
        $this->{self::MODIFIER_OPTION_ID} = $data[self::MODIFIER_OPTION_ID];
        $this->{self::MODIFIER_OPTION_NAME} = $data[self::MODIFIER_OPTION_NAME];
        $this->{self::MODIFIER_OPTION_PRICE} = $data[self::MODIFIER_OPTION_PRICE];
    }

}

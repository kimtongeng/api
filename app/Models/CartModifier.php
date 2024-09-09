<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CartModifier extends Model
{
    const TABLE_NAME = 'cart_modifier';
    const ID = 'id';
    const CART_ID = 'cart_id';
    const PRODUCT_MODIFIER_ID = 'product_modifier_id';
    const PRODUCT_MODIFIER_OPTION_ID = 'product_modifier_option_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::CART_ID} = $data[self::CART_ID];
        $this->{self::PRODUCT_MODIFIER_ID} = $data[self::PRODUCT_MODIFIER_ID];
        $this->{self::PRODUCT_MODIFIER_OPTION_ID} = $data[self::PRODUCT_MODIFIER_OPTION_ID];
    }
}

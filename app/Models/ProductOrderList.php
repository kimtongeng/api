<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrderList extends Model
{

    const TABLE_NAME = 'product_order_list';
    const ID = 'id';
    const TRANSACTION_ID = 'transaction_id';
    const PRODUCT_ID = 'product_id';
    const QTY = 'qty';
    const PRICE = 'price';
    const TOTAL_PRICE = 'total_price';
    const CONCAT_MODIFIER = 'concat_modifier';
    const CONCAT_VARIANT = 'concat_variant';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::TRANSACTION_ID} = $data[self::TRANSACTION_ID];
        $this->{self::PRODUCT_ID} = $data[self::PRODUCT_ID];
        $this->{self::QTY} = $data[self::QTY];
        $this->{self::PRICE} = $data[self::PRICE];
        isset($data[self::TOTAL_PRICE]) && $this->{self::TOTAL_PRICE} = $data[self::TOTAL_PRICE];
        isset($data[self::CONCAT_MODIFIER]) && $this->{self::CONCAT_MODIFIER} = $data[self::CONCAT_MODIFIER];
    }

    /*
     * Relationship Area
     * */
    //Product Order Modifier Option Relationship
    public function productOrderModifierOption()
    {
        return $this->hasMany(ProductOrderModifierOption::class, ProductOrderModifierOption::PRODUCT_ORDER_LIST_ID, self::ID);
    }
}

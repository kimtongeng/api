<?php

namespace App\Models;

use App\Enums\Types\DiscountType;
use Illuminate\Database\Eloquent\Model;

class PlacePriceList extends Model
{
    const TABLE_NAME = 'place_price_list';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const CATEGORY_ID = 'category_id';
    const IMAGE = 'image';
    const NAME = 'name';
    const PRICE = 'price';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_TYPE = 'discount_type';
    const SELL_PRICE = 'sell_price';
    const OPTION = 'option';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //set data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CATEGORY_ID} = $data[self::CATEGORY_ID];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::PRICE} = $data[self::PRICE];
        isset($data[self::DISCOUNT_AMOUNT]) && $this->{self::DISCOUNT_AMOUNT} = $data[self::DISCOUNT_AMOUNT];
        isset($data[self::DISCOUNT_TYPE]) && $this->{self::DISCOUNT_TYPE} = $data[self::DISCOUNT_TYPE];
        isset($data[self::SELL_PRICE]) && $this->{self::SELL_PRICE} = $data[self::SELL_PRICE];
        isset($data[self::DESCRIPTION]) && $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
        $this->{self::OPTION} = $data[self::OPTION];
    }

    //Get Sell Price with discount or not
    public function getSellPrice()
    {
        $sellPrice = $this->{self::PRICE};
        if ($this->{self::DISCOUNT_TYPE} == DiscountType::getAmount()) {
            $sellPrice -= $this->{self::DISCOUNT_AMOUNT};
        } elseif ($this->{self::DISCOUNT_TYPE} == DiscountType::getPercentage()) {
            $sellPrice -= ($this->{self::PRICE} * $this->{self::DISCOUNT_AMOUNT} / 100);
        }
        return $sellPrice;
    }
}

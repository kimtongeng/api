<?php

namespace App\Models;

use App\Enums\Types\BusinessTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class Cart extends Model
{
    const TABLE_NAME = 'cart';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const ITEM_ID = 'item_id';
    const QUANTITY = 'qty';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::ITEM_ID} = $data[self::ITEM_ID];
        $this->{self::QUANTITY} = $data[self::QUANTITY];
    }

    /**
     * Relationship
     *
     */
    //Product Modifier List
    public function cartModifierList()
    {
        return $this->hasMany(CartModifier::class, 'cart_id', 'id')
            ->join('modifier', 'modifier.id', 'cart_modifier.product_modifier_id')
            ->join('modifier_option', 'modifier_option.id', 'cart_modifier.product_modifier_option_id');
    }

    /**
     * List Cart
     *
     */
    public static function listCart($filter = [])
    {
        $contactID = isset($filter['contact_id']) ? $filter['contact_id'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;


        return self::leftjoin(
                'product',
                function ($join) {
                    $join->on('product.id', '=', 'cart.item_id')
                        ->where(function ($query) {
                            $query->where('cart.business_type_id', BusinessTypeEnum::getShopRetail())
                                ->orWhere('cart.business_type_id', BusinessTypeEnum::getShopWholesale())
                                ->orWhere('cart.business_type_id', BusinessTypeEnum::getRestaurant())
                                ->orWhere('cart.business_type_id', BusinessTypeEnum::getShopLocalProduct())
                                ->orWhere('cart.business_type_id', BusinessTypeEnum::getService())
                                ->orWhere('cart.business_type_id', BusinessTypeEnum::getModernCommunity());
                        })
                        ->whereNull('product.deleted_at');
                }
            )
            ->join('business', 'business.id', 'cart.business_id')
            ->leftjoin('cart_modifier', 'cart.id', 'cart_modifier.cart_id')
            ->leftjoin('modifier_option', 'modifier_option.id', 'cart_modifier.product_modifier_option_id')
            ->leftjoin('product as main', 'main.id', 'product.parent_id')
            ->select(
                'cart.id',
                'business.id as business_id',
                'business.name as business_name',
                'cart.business_type_id',
                'cart.contact_id',
                'cart.item_id',
                'cart.qty',
                DB::raw("
                CASE WHEN product.id IS NOT NULL
                THEN product.name
                ELSE ''
                END name
                "),
                DB::raw("
                CASE WHEN product.id IS NOT NULL
                THEN product.sell_price
                ELSE ''
                END price
                "),
                // Use COALESCE to get the main product image if the variant image is null
                DB::raw("
                CASE WHEN product.id IS NOT NULL
                THEN COALESCE(product.image, main.image)
                ELSE ''
                END image
                "),
                'product.parent_id',
                DB::raw('product.sell_price + COALESCE(SUM(modifier_option.price), 0) as sub_total'),
                'main.id as main_id',
                'main.name as main_name',
                'cart.created_at',
            )
            ->when($contactID, function ($query) use ($contactID) {
                $query->where('cart.contact_id', $contactID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('cart.business_id', $businessID);
            })
            ->when($businessTypeID, function ($query) use ($businessTypeID) {
                $query->where('cart.business_type_id', $businessTypeID);
            })
            ->with([
                'cartModifierList' => function ($query) {
                    $query->select(
                            'cart_modifier.id',
                            'cart_modifier.cart_id',
                            'modifier.id as modifier_id',
                            'modifier.name as modifier_name',
                            'modifier_option.id as modifier_option_id',
                            'modifier_option.name as modifier_option_name',
                            'modifier_option.price as modifier_option_price',
                        )
                        ->get();
                }
            ])
            ->groupBy('cart.id')
            ->orderBy('cart.id', 'DESC');
    }
}

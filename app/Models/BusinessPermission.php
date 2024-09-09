<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BusinessPermission extends Model
{
    //Table & Columns
    const TABLE_NAME = 'business_permission';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const NAME = 'name';
    const ACTION = 'action';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Permission Action Property
    const VIEW_PROPERTY = 'view_property';
    const CREATE_ASSET = 'create_asset';
    const EDIT_PROPERTY = 'edit_property';
    const DELETE_PROPERTY = 'delete_property';
    const VIEW_SALE_LIST_PROPERTY = 'view_sale_list_property';
    const CONFIRM_BOOKING_PROPERTY = 'confirm_booking_property';
    const REJECT_BOOKING_PROPERTY = 'reject_booking_property';
    const COMPLETE_BOOKING_PROPERTY = 'complete_booking_property';
    const VIEW_COMMISSION_LIST_PROPERTY = 'view_commission_list_property';
    const WITHDRAW_COMMISSION_PROPERTY = 'withdraw_commission_property';
    const CHAT_WITH_CUSTOMER_PROPERTY = 'chat_with_customer_property';
    //Permission Action Shop
    const VIEW_SHOP = 'view_shop';
    const EDIT_SHOP = 'edit_shop';
    const VIEW_PRODUCT_SHOP = 'view_product_shop';
    const CREATE_PRODUCT_SHOP = 'create_product_shop';
    const EDIT_PRODUCT_SHOP = 'edit_product_shop';
    const DELETE_PRODUCT_SHOP = 'delete_product_shop';
    const VIEW_CATEGORY_SHOP = 'view_category_shop';
    const CREATE_CATEGORY_SHOP = 'create_category_shop';
    const EDIT_CATEGORY_SHOP = 'edit_category_shop';
    const DELETE_CATEGORY_SHOP = 'delete_category_shop';
    const VIEW_SUB_CATEGORY_SHOP = 'view_sub_category_shop';
    const CREATE_SUB_CATEGORY_SHOP = 'create_sub_category_shop';
    const EDIT_SUB_CATEGORY_SHOP = 'edit_sub_category_shop';
    const DELETE_SUB_CATEGORY_SHOP = 'delete_sub_category_shop';
    const VIEW_BRAND_SHOP = 'view_brand_shop';
    const CREATE_BRAND_SHOP = 'create_brand_shop';
    const EDIT_BRAND_SHOP = 'edit_brand_shop';
    const DELETE_BRAND_SHOP = 'delete_brand_shop';
    const VIEW_MODIFIER_SHOP = 'view_modifier_shop';
    const CREATE_MODIFIER_SHOP = 'create_modifier_shop';
    const EDIT_MODIFIER_SHOP = 'edit_modifier_shop';
    const DELETE_MODIFIER_SHOP = 'delete_modifier_shop';
    const VIEW_SALE_LIST_SHOP = 'view_sale_list_shop';
    const VIEW_VARIANT_SHOP = 'view_variant_shop';
    const CREATE_VARIANT_SHOP = 'create_variant_shop';
    const EDIT_VARIANT_SHOP = 'edit_variant_shop';
    const DELETE_VARIANT_SHOP = 'delete_variant_shop';
    const APPROVE_ORDER_PRODUCT_SHOP = 'approve_order_product_shop';
    const REJECT_ORDER_PRODUCT_SHOP = 'reject_order_product_shop';
    const CHAT_WITH_CUSTOMER_SHOP = 'chat_with_customer_shop';
    //Permission Action Accommodation
    const VIEW_ACCOMMODATION = 'view_accommodation';
    const EDIT_ACCOMMODATION = 'edit_accommodation';
    const EDIT_ROOM_ACCOMMODATION = 'edit_room_accommodation';
    const DELETE_ROOM_ACCOMMODATION = 'delete_room_accommodation';
    const VIEW_BOOKING_LIST_ACCOMMODATION = 'view_booking_list_accommodation';
    const APPROVE_BOOKING_ACCOMMODATION = 'approve_booking_accommodation';
    const REJECT_BOOKING_ACCOMMODATION = 'reject_booking_accommodation';
    //Permission Action Massage
    const VIEW_MASSAGE_SHOP = 'view_massage_shop';
    const UPDATE_MASSAGE_SHOP = 'update_massage_shop';
    const ADD_MASSAGE_SERVICE = 'add_massage_service';
    const UPDATE_MASSAGE_SERVICE = 'update_massage_service';
    const REMOVE_MASSAGE_SERVICE = 'remove_massage_service';
    const ADD_MASSAGE_THERAPIST = 'add_massage_therapist';
    const UPDATE_MASSAGE_THERAPIST = 'update_massage_therapist';
    const REMOVE_MASSAGE_THERAPIST = 'remove_massage_therapist';
    const VIEW_SALE_LIST_MASSAGE = 'view_sale_list_massage';
    const CONFIRM_BOOKING_MASSAGE = 'confirm_booking_massage';
    const REJECT_BOOKING_MASSAGE = 'reject_booking_massage';
    //Permission Action Attraction
    const VIEW_ATTRACTION = 'view_attraction';
    const UPDATE_ATTRACTION = 'update_attraction';
    const VIEW_SALE_LIST_ATTRACTION = 'view_sale_list_attraction';
    const CONFIRM_BOOKING_ATTRACTION = 'confirm_attraction_booking';
    const REJECT_BOOKING_ATTRACTION = 'reject_attraction_booking';
    //Permission Action KTV
    const VIEW_KTV = 'view_ktv';
    const UPDATE_KTV = 'update_ktv';
    const ADD_KTV_CATEGORY = 'add_ktv_category';
    const UPDATE_KTV_CATEGORY = 'update_ktv_category';
    const DELETE_KTV_CATEGORY = 'delete_ktv_category';
    const ADD_KTV_PRODUCT = 'add_ktv_product';
    const UPDATE_KTV_PRODUCT = 'update_ktv_product';
    const DELETE_KTV_PRODUCT = 'delete_ktv_product';
    const ADD_KTV_GIRL = 'add_ktv_girl';
    const UPDATE_KTV_GIRL = 'update_ktv_girl';
    const DELETE_KTV_GIRL = 'delete_ktv_girl';
    const ADD_KTV_ROOM = 'add_ktv_room';
    const UPDATE_KTV_ROOM = 'update_ktv_room';
    const DELETE_KTV_ROOM = 'delete_ktv_room';
    const VIEW_SALE_LIST_KTV = 'view_sale_list_ktv';
    const CONFIRM_BOOKING_KTV = 'confirm_booking_ktv';
    const REJECT_BOOKING_KTV = 'rejected_booking_ktv';

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::ACTION} = $data[self::ACTION];
    }

    //list
    public static function lists($filter = [])
    {
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;

        return self::select(
                'business_permission.id',
                'business_permission.name',
                'business_permission.action',
            )
            ->when($businessTypeID, function ($query) use ($businessTypeID) {
                $query->where('business_permission.business_type_id', $businessTypeID);
            })
            ->orderBy('business_permission.created_at', 'ASC');
    }

}

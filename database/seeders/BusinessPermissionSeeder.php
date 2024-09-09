<?php

namespace Database\Seeders;

use App\Enums\Types\BusinessTypeEnum;
use App\Models\Business;
use App\Models\BusinessPermission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(BusinessPermission::TABLE_NAME)->truncate();

        DB::table(BusinessPermission::TABLE_NAME)->insert([
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីអចលនទ្រព្យ","latin_name":"Can View Property"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតទ្រព្យសកម្ម","latin_name":"Can Create Asset"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_ASSET,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែអចលនទ្រព្យ","latin_name":"Can Edit Property"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបអចលនទ្រព្យ","latin_name":"Can Delete Property"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់","latin_name":"Can View Sale List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចបញ្ជាក់ការកក់","latin_name":"Can Confirm Booking"}',
                BusinessPermission::ACTION => BusinessPermission::CONFIRM_BOOKING_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការកក់","latin_name":"Can Reject Booking"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_BOOKING_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចមបញ្ចប់ការកក់","latin_name":"Can Complete Booking"}',
                BusinessPermission::ACTION => BusinessPermission::COMPLETE_BOOKING_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីកម្រៃជើងសារ","latin_name":"Can View Commission List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_COMMISSION_LIST_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចដកកម្រៃជើងសារ","latin_name":"Can Withdraw Commission"}',
                BusinessPermission::ACTION => BusinessPermission::WITHDRAW_COMMISSION_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessPermission::NAME => '{"local_name":"អាចជជែកជាមួយអតិថិជន","latin_name":"Can Chat With Customer"}',
                BusinessPermission::ACTION => BusinessPermission::CHAT_WITH_CUSTOMER_PROPERTY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីហាង","latin_name":"Can View Shop"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែហាង","latin_name":"Can Edit Shop"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីផលិតផល","latin_name":"Can View Product"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតផលិតផល","latin_name":"Can Create Product"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែផលិតផល","latin_name":"Can Edit Product"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបផលិតផល","latin_name":"Can Delete Product"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទ","latin_name":"Can View Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទ","latin_name":"Can Create Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទ","latin_name":"Can Edit Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទ","latin_name":"Can Delete Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទរង","latin_name":"Can View Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទរង","latin_name":"Can Create Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទរង","latin_name":"Can Edit Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទរង","latin_name":"Can Delete Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីម៉ាក","latin_name":"Can View Brand"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតម៉ាក","latin_name":"Can Create Brand"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែម៉ាក","latin_name":"Can Edit Brand"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបម៉ាក","latin_name":"Can Delete Brand"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីការបន្ថែម","latin_name":"Can View Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតការបន្ថែម","latin_name":"Can Create Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែការបន្ថែម","latin_name":"Can Edit Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបការបន្ថែម","latin_name":"Can Delete Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលវ៉ារ្យ៉ង់","latin_name":"Can View Variant"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតវ៉ារ្យ៉ង់","latin_name":"Can Create Variant"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែវ៉ារ្យ៉ង់","latin_name":"Can Edit Variant"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបវ៉ារ្យ៉ង់","latin_name":"Can Delete Variant"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់","latin_name":"Can View Sale List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចយល់ព្រមការបញ្ជាទិញ","latin_name":"Can Approve Order"}',
                BusinessPermission::ACTION => BusinessPermission::APPROVE_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការបញ្ជាទិញ","latin_name":"Can Reject Order"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopRetail(),
                BusinessPermission::NAME => '{"local_name":"អាចជជែកជាមួយអតិថិជន","latin_name":"Can Chat With Customer"}',
                BusinessPermission::ACTION => BusinessPermission::CHAT_WITH_CUSTOMER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីហាង","latin_name":"Can View Shop"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែហាង","latin_name":"Can Edit Shop"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីផលិតផល","latin_name":"Can View Product"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតផលិតផល","latin_name":"Can Create Product"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែផលិតផល","latin_name":"Can Edit Product"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបផលិតផល","latin_name":"Can Delete Product"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទ","latin_name":"Can View Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទ","latin_name":"Can Create Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទ","latin_name":"Can Edit Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទ","latin_name":"Can Delete Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទរង","latin_name":"Can View Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទរង","latin_name":"Can Create Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទរង","latin_name":"Can Edit Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទរង","latin_name":"Can Delete Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីម៉ាក","latin_name":"Can View Brand"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតម៉ាក","latin_name":"Can Create Brand"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែម៉ាក","latin_name":"Can Edit Brand"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបម៉ាក","latin_name":"Can Delete Brand"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីការបន្ថែម","latin_name":"Can View Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតការបន្ថែម","latin_name":"Can Create Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែការបន្ថែម","latin_name":"Can Edit Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបការបន្ថែម","latin_name":"Can Delete Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលវ៉ារ្យ៉ង់","latin_name":"Can View Variant"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតវ៉ារ្យ៉ង់","latin_name":"Can Create Variant"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែវ៉ារ្យ៉ង់","latin_name":"Can Edit Variant"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបវ៉ារ្យ៉ង់","latin_name":"Can Delete Variant"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់","latin_name":"Can View Sale List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចយល់ព្រមការបញ្ជាទិញ","latin_name":"Can Approve Order"}',
                BusinessPermission::ACTION => BusinessPermission::APPROVE_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការបញ្ជាទិញ","latin_name":"Can Reject Order"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopWholesale(),
                BusinessPermission::NAME => '{"local_name":"អាចជជែកជាមួយអតិថិជន","latin_name":"Can Chat With Customer"}',
                BusinessPermission::ACTION => BusinessPermission::CHAT_WITH_CUSTOMER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីហាង","latin_name":"Can View Shop"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែហាង","latin_name":"Can Edit Shop"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីផលិតផល","latin_name":"Can View Product"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតផលិតផល","latin_name":"Can Create Product"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែផលិតផល","latin_name":"Can Edit Product"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបផលិតផល","latin_name":"Can Delete Product"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទ","latin_name":"Can View Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទ","latin_name":"Can Create Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទ","latin_name":"Can Edit Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទ","latin_name":"Can Delete Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទរង","latin_name":"Can View Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទរង","latin_name":"Can Create Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទរង","latin_name":"Can Edit Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទរង","latin_name":"Can Delete Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីម៉ាក","latin_name":"Can View Brand"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតម៉ាក","latin_name":"Can Create Brand"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែម៉ាក","latin_name":"Can Edit Brand"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបម៉ាក","latin_name":"Can Delete Brand"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីការបន្ថែម","latin_name":"Can View Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតការបន្ថែម","latin_name":"Can Create Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែការបន្ថែម","latin_name":"Can Edit Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបការបន្ថែម","latin_name":"Can Delete Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលវ៉ារ្យ៉ង់","latin_name":"Can View Variant"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតវ៉ារ្យ៉ង់","latin_name":"Can Create Variant"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែវ៉ារ្យ៉ង់","latin_name":"Can Edit Variant"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបវ៉ារ្យ៉ង់","latin_name":"Can Delete Variant"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់","latin_name":"Can View Sale List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចយល់ព្រមការបញ្ជាទិញ","latin_name":"Can Approve Order"}',
                BusinessPermission::ACTION => BusinessPermission::APPROVE_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការបញ្ជាទិញ","latin_name":"Can Reject Order"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getRestaurant(),
                BusinessPermission::NAME => '{"local_name":"អាចជជែកជាមួយអតិថិជន","latin_name":"Can Chat With Customer"}',
                BusinessPermission::ACTION => BusinessPermission::CHAT_WITH_CUSTOMER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីហាង","latin_name":"Can View Shop"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែហាង","latin_name":"Can Edit Shop"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីផលិតផល","latin_name":"Can View Product"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតផលិតផល","latin_name":"Can Create Product"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែផលិតផល","latin_name":"Can Edit Product"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបផលិតផល","latin_name":"Can Delete Product"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទ","latin_name":"Can View Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទ","latin_name":"Can Create Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទ","latin_name":"Can Edit Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទ","latin_name":"Can Delete Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទរង","latin_name":"Can View Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទរង","latin_name":"Can Create Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទរង","latin_name":"Can Edit Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទរង","latin_name":"Can Delete Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីម៉ាក","latin_name":"Can View Brand"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតម៉ាក","latin_name":"Can Create Brand"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែម៉ាក","latin_name":"Can Edit Brand"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបម៉ាក","latin_name":"Can Delete Brand"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីការបន្ថែម","latin_name":"Can View Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតការបន្ថែម","latin_name":"Can Create Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែការបន្ថែម","latin_name":"Can Edit Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបការបន្ថែម","latin_name":"Can Delete Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលវ៉ារ្យ៉ង់","latin_name":"Can View Variant"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតវ៉ារ្យ៉ង់","latin_name":"Can Create Variant"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែវ៉ារ្យ៉ង់","latin_name":"Can Edit Variant"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបវ៉ារ្យ៉ង់","latin_name":"Can Delete Variant"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់","latin_name":"Can View Sale List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចយល់ព្រមការបញ្ជាទិញ","latin_name":"Can Approve Order"}',
                BusinessPermission::ACTION => BusinessPermission::APPROVE_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការបញ្ជាទិញ","latin_name":"Can Reject Order"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getShopLocalProduct(),
                BusinessPermission::NAME => '{"local_name":"អាចជជែកជាមួយអតិថិជន","latin_name":"Can Chat With Customer"}',
                BusinessPermission::ACTION => BusinessPermission::CHAT_WITH_CUSTOMER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីហាង","latin_name":"Can View Shop"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែហាង","latin_name":"Can Edit Shop"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីផលិតផល","latin_name":"Can View Product"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតផលិតផល","latin_name":"Can Create Product"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែផលិតផល","latin_name":"Can Edit Product"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបផលិតផល","latin_name":"Can Delete Product"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទ","latin_name":"Can View Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទ","latin_name":"Can Create Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទ","latin_name":"Can Edit Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទ","latin_name":"Can Delete Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទរង","latin_name":"Can View Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទរង","latin_name":"Can Create Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទរង","latin_name":"Can Edit Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទរង","latin_name":"Can Delete Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីម៉ាក","latin_name":"Can View Brand"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតម៉ាក","latin_name":"Can Create Brand"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែម៉ាក","latin_name":"Can Edit Brand"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបម៉ាក","latin_name":"Can Delete Brand"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីការបន្ថែម","latin_name":"Can View Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតការបន្ថែម","latin_name":"Can Create Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែការបន្ថែម","latin_name":"Can Edit Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបការបន្ថែម","latin_name":"Can Delete Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលវ៉ារ្យ៉ង់","latin_name":"Can View Variant"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតវ៉ារ្យ៉ង់","latin_name":"Can Create Variant"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែវ៉ារ្យ៉ង់","latin_name":"Can Edit Variant"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបវ៉ារ្យ៉ង់","latin_name":"Can Delete Variant"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់","latin_name":"Can View Sale List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចយល់ព្រមការបញ្ជាទិញ","latin_name":"Can Approve Order"}',
                BusinessPermission::ACTION => BusinessPermission::APPROVE_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការបញ្ជាទិញ","latin_name":"Can Reject Order"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getService(),
                BusinessPermission::NAME => '{"local_name":"អាចជជែកជាមួយអតិថិជន","latin_name":"Can Chat With Customer"}',
                BusinessPermission::ACTION => BusinessPermission::CHAT_WITH_CUSTOMER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីហាង","latin_name":"Can View Shop"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែហាង","latin_name":"Can Edit Shop"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីផលិតផល","latin_name":"Can View Product"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតផលិតផល","latin_name":"Can Create Product"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែផលិតផល","latin_name":"Can Edit Product"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបផលិតផល","latin_name":"Can Delete Product"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទ","latin_name":"Can View Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទ","latin_name":"Can Create Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទ","latin_name":"Can Edit Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទ","latin_name":"Can Delete Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីប្រភេទរង","latin_name":"Can View Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតប្រភេទរង","latin_name":"Can Create Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែប្រភេទរង","latin_name":"Can Edit Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបប្រភេទរង","latin_name":"Can Delete Sub Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_SUB_CATEGORY_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីម៉ាក","latin_name":"Can View Brand"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតម៉ាក","latin_name":"Can Create Brand"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែម៉ាក","latin_name":"Can Edit Brand"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបម៉ាក","latin_name":"Can Delete Brand"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_BRAND_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីការបន្ថែម","latin_name":"Can View Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតការបន្ថែម","latin_name":"Can Create Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែការបន្ថែម","latin_name":"Can Edit Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបការបន្ថែម","latin_name":"Can Delete Modifier"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_MODIFIER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលវ៉ារ្យ៉ង់","latin_name":"Can View Variant"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចបង្កើតវ៉ារ្យ៉ង់","latin_name":"Can Create Variant"}',
                BusinessPermission::ACTION => BusinessPermission::CREATE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែវ៉ារ្យ៉ង់","latin_name":"Can Edit Variant"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបវ៉ារ្យ៉ង់","latin_name":"Can Delete Variant"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_VARIANT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់","latin_name":"Can View Sale List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចយល់ព្រមការបញ្ជាទិញ","latin_name":"Can Approve Order"}',
                BusinessPermission::ACTION => BusinessPermission::APPROVE_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការបញ្ជាទិញ","latin_name":"Can Reject Order"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_ORDER_PRODUCT_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getModernCommunity(),
                BusinessPermission::NAME => '{"local_name":"អាចជជែកជាមួយអតិថិជន","latin_name":"Can Chat With Customer"}',
                BusinessPermission::ACTION => BusinessPermission::CHAT_WITH_CUSTOMER_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលកន្លែងស្នាក់នៅ","latin_name":"Can View Accommodation"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_ACCOMMODATION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែកន្លែងស្នាក់នៅ","latin_name":"Can Edit Accommodation"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_ACCOMMODATION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែបន្ទប់ស្នាក់នៅ","latin_name":"Can Edit Room"}',
                BusinessPermission::ACTION => BusinessPermission::EDIT_ROOM_ACCOMMODATION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបបន្ទប់ស្នាក់នៅបាន","latin_name":"Can Delete Room"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_ROOM_ACCOMMODATION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីកក់កន្លែងស្នាក់នៅ","latin_name":"Can View Booking List"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_BOOKING_LIST_ACCOMMODATION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessPermission::NAME => '{"local_name":"អាចអនុម័តការកក់កន្លែងស្នាក់នៅ","latin_name":"Can Approve Booking"}',
                BusinessPermission::ACTION => BusinessPermission::APPROVE_BOOKING_ACCOMMODATION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការកក់កន្លែងស្នាក់នៅ","latin_name":"Can Reject Booking"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_BOOKING_ACCOMMODATION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលហាងម៉ាស្សា","latin_name":"Can View Massage Shop"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_MASSAGE_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែហាងម៉ាស្សាបាន។","latin_name":"Can Update Massage Shop"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_MASSAGE_SHOP,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចបន្ថែមសេវាកម្មម៉ាស្សា","latin_name":"Can Add Massage Service"}',
                BusinessPermission::ACTION => BusinessPermission::ADD_MASSAGE_SERVICE,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែសេវាកម្មម៉ាស្សា","latin_name":"Can Update Massage Service"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_MASSAGE_SERVICE,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបសេវាម៉ាស្សា","latin_name":"Can Remove Massage Service"}',
                BusinessPermission::ACTION => BusinessPermission::REMOVE_MASSAGE_SERVICE,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចបន្ថែមអ្នកម៉ាស្សា","latin_name":"Can Add Massage Therapist"}',
                BusinessPermission::ACTION => BusinessPermission::ADD_MASSAGE_THERAPIST,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែអ្នកម៉ាស្សា","latin_name":"Can Update Massage Therapist"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_MASSAGE_THERAPIST,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចលុបអ្នកម៉ាស្សាបាន","latin_name":"Can Remove Massage Therapist"}',
                BusinessPermission::ACTION => BusinessPermission::REMOVE_MASSAGE_THERAPIST,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់ម៉ាស្សា","latin_name":"Can View Sale List Massage"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_MASSAGE,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចបញ្ជាក់ការកក់ម៉ាស្សា","latin_name":"Can Confirm Booking Massage"}',
                BusinessPermission::ACTION => BusinessPermission::CONFIRM_BOOKING_MASSAGE,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការកក់ម៉ាស្សា","latin_name":"Can Reject Booking Massage"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_BOOKING_MASSAGE,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAttraction(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលតំបន់ទេសចរណ៍","latin_name":"Can View Attraction"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_ATTRACTION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAttraction(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែតំបន់ទេសចរណ៍","latin_name":"Can Update Attraction"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_ATTRACTION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAttraction(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់តំបន់ទេសចរណ៍","latin_name":"Can View Sale List Attraction"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_ATTRACTION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAttraction(),
                BusinessPermission::NAME => '{"local_name":"អាចបញ្ជាក់ការកក់តំបន់ទេសចរណ៍","latin_name":"Confirm Booking Attraction"}',
                BusinessPermission::ACTION => BusinessPermission::CONFIRM_BOOKING_ATTRACTION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getAttraction(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការកក់តំបន់ទេសចរណ៍","latin_name":"Reject Booking Attraction"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_BOOKING_ATTRACTION,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចមើល KTV","latin_name":"Can View KTV"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_KTV,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែ KTV","latin_name":"Can Update KTV"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_KTV,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចបន្ថែម KTV ប្រភេទ","latin_name":"Can Add KTV Category"}',
                BusinessPermission::ACTION => BusinessPermission::ADD_KTV_CATEGORY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែ KTV ប្រភេទ","latin_name":"Can Update KTV Category"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_KTV_CATEGORY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចលុប KTV ប្រភេទ","latin_name":"Can Delete KTV Category"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_KTV_CATEGORY,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចបន្ថែម KTV ផលិតផល","latin_name":"Can Add KTV Product"}',
                BusinessPermission::ACTION => BusinessPermission::ADD_KTV_PRODUCT,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែ KTV ផលិតផល","latin_name":"Can Update KTV Product"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_KTV_PRODUCT,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចលុប KTV ផលិតផល","latin_name":"Can Delete KTV Product"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_KTV_PRODUCT,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចបន្ថែម KTV នារី","latin_name":"Can Add KTV Girl"}',
                BusinessPermission::ACTION => BusinessPermission::ADD_KTV_GIRL,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែ KTV នារី","latin_name":"Can Update KTV Girl"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_KTV_GIRL,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចលុប KTV នារី","latin_name":"Can Delete KTV Girl"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_KTV_GIRL,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចបន្ថែម KTV បន្ទប់","latin_name":"Can Add KTV Room"}',
                BusinessPermission::ACTION => BusinessPermission::ADD_KTV_ROOM,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចកែប្រែ KTV បន្ទប់","latin_name":"Can Update KTV Room"}',
                BusinessPermission::ACTION => BusinessPermission::UPDATE_KTV_ROOM,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចលុប KTV បន្ទប់","latin_name":"Can Delete KTV Room"}',
                BusinessPermission::ACTION => BusinessPermission::DELETE_KTV_ROOM,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចមើលបញ្ជីលក់ KTV","latin_name":"Can View Sale List KTV"}',
                BusinessPermission::ACTION => BusinessPermission::VIEW_SALE_LIST_KTV,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចបញ្ជាក់ការកក់ KTV","latin_name":"Can Confrim Booking KTV"}',
                BusinessPermission::ACTION => BusinessPermission::CONFIRM_BOOKING_KTV,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
            [
                BusinessPermission::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                BusinessPermission::NAME => '{"local_name":"អាចបដិសេធការកក់ KTV","latin_name":"Can Reject Booking KTV"}',
                BusinessPermission::ACTION => BusinessPermission::REJECT_BOOKING_KTV,
                BusinessPermission::CREATED_AT => Carbon::now(),
                BusinessPermission::UPDATED_AT => Carbon::now()
            ],
        ]);
    }
}

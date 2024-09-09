<?php

namespace App\Enums;

use App\Enums\Types\AdminNotificationType;
use App\Enums\Types\AttributeStatus;
use App\Enums\Types\BankAccountStatus;
use App\Enums\Types\BannerImageType;
use App\Enums\Types\BannerPage;
use App\Enums\Types\BannerPlatformType;
use App\Enums\Types\BusinessActive;
use App\Enums\Types\BusinessStatus;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\ChatType;
use App\Enums\Types\CommissionType;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\GeneralSettingKey;
use App\Enums\Types\IsBusinessOwner;
use App\Enums\Types\NotificationReadType;
use App\Enums\Types\NotificationSendType;
use App\Enums\Types\PlaceContactType;
use App\Enums\Types\PositionStatus;
use App\Enums\Types\BannerStatus;
use App\Enums\Types\BannerType;
use App\Enums\Types\ContactGender;
use App\Enums\Types\ContactStatus;
use App\Enums\Types\IsFreeDelivery;
use App\Enums\Types\IsHasVariant;
use App\Enums\Types\IsOpen24Hour;
use App\Enums\Types\IsRequiredModifier;
use App\Enums\Types\IsTrackStock;
use App\Enums\Types\ModifierChoice;
use App\Enums\Types\PositionBannerList;
use App\Enums\Types\PositionPlatformType;
use App\Enums\Types\PositionVideoList;
use App\Enums\Types\PropertyAssetStatus;
use App\Enums\Types\PropertyTypeEnum;
use App\Enums\Types\TransactionFeeStatus;
use App\Enums\Types\TransactionStatus;
use App\Enums\Types\VideoPage;
use App\Enums\Types\VideoStatus;
use App\Enums\Types\BusinessCategoryStatus;
use App\Enums\Types\BusinessTypeHasTransaction;
use App\Enums\Types\BusinessTypeStatus;
use App\Enums\Types\CountryStatus;
use App\Enums\Types\LocationStatus;
use App\Enums\Types\MassageServiceType;
use App\Enums\Types\ProductStatus;
use App\Enums\Types\VehicleTypeStatus;

class CollectionEnum
{
    public static function getForAdmin()
    {
        return [
            'general_setting_key' => GeneralSettingKey::getByEachName(),
            'chat_type' => ChatType::getByEachName(),
            'notification_status' => NotificationReadType::getByEachName(),
            'notification_send_type' => NotificationSendType::getByEachName(),
            'admin_notification_type' => AdminNotificationType::getByEachName(),
            'contact_status' => ContactStatus::getByEachName(),
            'contact_gender' => ContactGender::getByEachName(),
            'banner_type_list' => BannerType::getComboList(),
            'banner_type' => BannerType::getByEachName(),
            'banner_status' => BannerStatus::getByEachName(),
            'banner_image_type' => BannerImageType::getByEachName(),
            'banner_platform_type' => BannerPlatformType::getByEachName(),
            'banner_page_list' => BannerPage::getComboList(),
            'banner_page' => BannerPage::getByEachName(),
            'position_status' => PositionStatus::getByEachName(),
            'position_platform_type' => PositionPlatformType::getByEachName(),
            'position_banner_list' => PositionBannerList::getComboList(),
            'is_true' => IsBusinessOwner::getByEachName(),
            'video_page_list' => VideoPage::getComboList(),
            'video_page' => VideoPage::getByEachName(),
            'video_status' => VideoStatus::getByEachName(),
            'position_video_list' => PositionVideoList::getComboList(),
            'contact_noti_type_list' => ContactNotificationType::getComboListBroadCast(),
            'contact_noti_type' => ContactNotificationType::getByEachName(),
            'bank_account_status' => BankAccountStatus::getByEachName(),
            'transaction_fee_status' => TransactionFeeStatus::getByEachName(),
            'transaction_fee_status_list' => TransactionFeeStatus::getComboList(),
            'business_type' => BusinessTypeEnum::getByEachName(),
            'business_type_list' => BusinessTypeEnum::getComboList(),
            'business_status' => BusinessStatus::getByEachName(),
            'business_active' => BusinessActive::getByEachName(),
            'property_asset_status' => PropertyAssetStatus::getByEachName(),
            'property_type' => PropertyTypeEnum::getByEachName(),
            'place_contact_type_list' => PlaceContactType::getComboList(),
            'transaction_status' => TransactionStatus::getByEachName(),
            'business_category_status' => BusinessCategoryStatus::getByEachName(),
            'commission_type' => CommissionType::getByEachName(),
            'is_free_delivery' => IsFreeDelivery::getByEachName(),
            'is_open_24_hour' => IsOpen24Hour::getByEachName(),
            'modifier_choice' => ModifierChoice::getByEachName(),
            'is_required_modifier' => IsRequiredModifier::getByEachName(),
            'is_track_stock' => IsTrackStock::getByEachName(),
            'is_has_variant' => IsHasVariant::getByEachName(),
            'attribute_status' => AttributeStatus::getByEachName(),
            'product_status' => ProductStatus::getByEachName(),
            'massage_service_type' => MassageServiceType::getByEachName(),
            'business_type_status' => BusinessTypeStatus::getByEachName(),
            'business_type_has_transaction' => BusinessTypeHasTransaction::getByEachName(),
            'location_status' => LocationStatus::getByEachName(),
            'vehicle_type_status' => VehicleTypeStatus::getByEachName(),
        ];
    }
}

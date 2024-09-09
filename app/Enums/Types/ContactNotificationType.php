<?php

namespace App\Enums\Types;

class ContactNotificationType
{
    //Declare Name And Value
    const LINK = [
        'id' => 1,
        'name' => 'link'
    ];
    const DETAIL = [
        'id' => 2,
        'name' => 'detail'
    ];
    const PROPERTY = [
        'id' => 3,
        'name' => 'property'
    ];
    const PROPERTY_DETAIL = [
        'id' => 4,
        'name' => 'property_detail'
    ];
    const PROPERTY_TYPE = [
        'id' => 5,
        'name' => 'property_type'
    ];
    const PROPERTY_BOOKING = [
        'id' => 6,
        'name' => 'property_booking'
    ];
    const PROPERTY_BOOKING_APPROVED = [
        'id' => 7,
        'name' => 'property_booking_approved'
    ];
    const PROPERTY_BOOKING_REJECTED = [
        'id' => 8,
        'name' => 'property_booking_rejected'
    ];
    const PROPERTY_BOOKING_CANCELLED = [
        'id' => 9,
        'name' => 'property_booking_cancelled'
    ];
    const PROPERTY_BOOKING_COMPLETED = [
        'id' => 10,
        'name' => 'property_booking_completed'
    ];
    const APP_BLOCKED = [
        'id' => 11,
        'name' => 'blocked_app'
    ];
    const OWNER_WITHDRAW_MULTI_PROPERTY_COMMISSION = [
        'id' => 12,
        'name' => 'owner_withdraw_multi_property_commission'
    ];
    const OWNER_PROPERTY_ADD_SALE_ASSISTANCE = [
        'id' => 13,
        'name' => 'owner_property_add_sale_assistance'
    ];
    const APP_UNBLOCKED = [
        'id' => 14,
        'name' => 'unblocked_app'
    ];
    const AGENCY_CONFIRMED_WITHDRAWN_MULTI_PROPERTY_COMMISSION = [
        'id' => 15,
        'name' => 'agency_confirmed_withdrawn_multi_property_commission'
    ];
    const AGENCY_REJECTED_WITHDRAWN_MULTI_PROPERTY_COMMISSION = [
        'id' => 16,
        'name' => 'agency_rejected_withdrawn_multi_property_commission'
    ];
    const PRODUCT_ORDER = [
        'id' => 17,
        'name' => 'product_order'
    ];
    const PRODUCT_ORDER_APPROVED = [
        'id' => 18,
        'name' => 'product_order_approved'
    ];
    const PRODUCT_ORDER_REJECTED = [
        'id' => 19,
        'name' => 'product_order_rejected'
    ];
    const PRODUCT_ORDER_CANCELLED = [
        'id' => 20,
        'name' => 'product_order_cancelled'
    ];
    const OWNER_WITHDRAW_SINGLE_PROPERTY_COMMISSION = [
        'id' => 21,
        'name' => 'owner_withdraw_single_property_commission'
    ];
    const AGENCY_CONFIRMED_WITHDRAWN_SINGLE_PROPERTY_COMMISSION = [
        'id' => 22,
        'name' => 'agency_confirmed_withdrawn_single_property_commission'
    ];
    const AGENCY_REJECTED_WITHDRAWN_SINGLE_PROPERTY_COMMISSION = [
        'id' => 23,
        'name' => 'agency_rejected_withdrawn_single_property_commission'
    ];
    const CHARITY_DONATION = [
        'id' => 24,
        'name' => 'charity_donation'
    ];
    const CHARITY_DONATION_APPROVED = [
        'id' => 25,
        'name' => 'charity_donation_approved'
    ];
    const CHARITY_DONATION_REJECTED = [
        'id' => 26,
        'name' => 'charity_donation_rejected'
    ];
    const ACCOMMODATION_BOOKING = [
        'id' => 27,
        'name' => 'accommodation_booking'
    ];
    const ACCOMMODATION_BOOKING_APPROVE = [
        'id' => 28,
        'name' => 'accommodation_booking_approve'
    ];
    const ACCOMMODATION_BOOKING_REJECT = [
        'id' => 29,
        'name' => 'accommodation_booking_reject'
    ];
    const ACCOMMODATION_BOOKING_CANCEL = [
        'id' => 30,
        'name' => 'accommodation_booking_cancel'
    ];
    const ACCOMMODATION_BOOKING_PAYMENT = [
        'id' => 31,
        'name' => 'accommodation_booking_payment'
    ];
    const ACCOMMODATION_BOOKING_REJECT_PAYMENT = [
        'id' => 32,
        'name' => 'accommodation_booking_reject_payment'
    ];
    const ACCOMMODATION_BOOKING_AUDITING_PAYMENT = [
        'id' => 33,
        'name' => 'accommodation_booking_auditing_payment'
    ];
    const MASSAGE_THERAPIST_ADD = [
        'id' => 34,
        'name' => 'massage_therapist_add'
    ];
    const MASSAGE_THERAPIST_APPROVE = [
        'id' => 35,
        'name' => 'massage_therapist_approve'
    ];
    const MASSAGE_THERAPIST_REJECT = [
        'id' => 36,
        'name' => 'massage_therapist_reject'
    ];
    const MASSAGE_SHOP_BOOKING = [
        'id' => 37,
        'name' => 'massage_shop_booking'
    ];
    const MASSAGE_SHOP_APPROVE = [
        'id' => 38,
        'name' => 'massage_shop_approve'
    ];
    const MASSAGE_SHOP_REJECT = [
        'id' => 39,
        'name' => 'massage_shop_reject'
    ];
    const MASSAGE_SHOP_CANCEL = [
        'id' => 40,
        'name' => 'massage_shop_cancel'
    ];
    const MASSAGE_SHOP_PAYMENT = [
        'id' => 41,
        'name' => 'massage_shop_payment'
    ];
    const MASSAGE_SHOP_REJECT_PAYMENT = [
        'id' => 42,
        'name' => 'massage_shop_reject_payment'
    ];
    const MASSAGE_SHOP_AUDITING_PAYMENT = [
        'id' => 43,
        'name' => 'massage_shop_auditing_payment'
    ];
    const MASSAGE_SHOP_FOR_MASSAGER = [
        'id' => 44,
        'name' => 'massage_shop_for_massager'
    ];
    const ATTRACTION_BOOKING = [
        'id' => 45,
        'name' => 'attraction_booking',
    ];
    const ATTRACTION_APPROVE = [
        'id' => 46,
        'name' => 'attraction_approve',
    ];
    const ATTRACTION_REJECT = [
        'id' => 47,
        'name' => 'attraction_reject',
    ];
    const ATTRACTION_CANCEL = [
        'id' => 48,
        'name' => 'attraction_cancel',
    ];
    const LATEST_NEWS = [
        'id' => 49,
        'name' => 'latest_news',
    ];
    const POSTER_COMMENT = [
        'id' => 50,
        'name' => 'POSTER_COMMENT',
    ];
    const PARTICIPANT_COMMENT = [
        'id' => 51,
        'name' => 'PARTICIPANT_COMMENT',
    ];
    const KTV_GIRL_ADD = [
        'id' => 52,
        'name' => 'KTV_GIRL_ADD',
    ];
    const KTV_GIRL_APPROVE = [
        'id' => 53,
        'name' => 'KTV_GIRL_APPROVE',
    ];
    const KTV_GIRL_REJECT = [
        'id' => 54,
        'name' => 'KTV_GIRL_REJECT',
    ];
    const KTV_BOOKING = [
        'id' => 55,
        'name' => 'ktv_booking',
    ];
    const KTV_APPROVE = [
        'id' => 56,
        'name' => 'ktv_approve',
    ];
    const KTV_REJECT = [
        'id' => 57,
        'name' => 'ktv_reject',
    ];
    const KTV_CANCEL = [
        'id' => 58,
        'name' => 'ktv_cancel',
    ];
    const KTV_ADD_PAYMENT = [
        'id' => 59,
        'name' => 'ktv_add_payment',
    ];
    const KTV_REJECT_PAYMENT = [
        'id' => 60,
        'name' => 'ktv_reject_payment',
    ];
    const KTV_AUDITING_PAYMENT = [
        'id' => 61,
        'name' => 'ktv_auditing_payment',
    ];
    const KTV_FOR_KTV_GIRL = [
        'id' => 62,
        'name' => 'ktv_for_ktv_girl',
    ];
    const SHARE_BUSINESS_PERMISSION = [
        'id' => 63,
        'name' => 'share_business_permission',
    ];
    const UPDATE_BUSINESS_PERMISSION = [
        'id' => 64,
        'name' => 'update_business_permission',
    ];
    const DELETE_BUSINESS_PERMISSION = [
        'id' => 65,
        'name' => 'delete_business_permission',
    ];
    const BOOKING_SHOP_BUSINESS_FOR_USER_SHARE = [
        'id' => 66,
        'name' => 'booking_shop_business_for_user_share',
    ];
    const CANCEL_SHOP_BUSINESS_FOR_USER_SHARE = [
        'id' => 67,
        'name' => 'cancel_shop_business_for_user_share',
    ];
    const MASSAGE_THERAPIST_UPDATE = [
        'id' => 68,
        'name' => 'massage_therapist_update',
    ];
    const KTV_GIRL_UPDATE = [
        'id' => 69,
        'name' => 'ktv_girl_update',
    ];
    //Get Combo List Broadcast (For Front)
    public static function getComboListBroadCast()
    {
        return [
            self::LINK,
            self::DETAIL,
            self::PROPERTY,
            self::PROPERTY_DETAIL,
            self::PROPERTY_TYPE
        ];
    }

    //Get Value By Function Name (For Api)
    public static function getLink()
    {
        return self::LINK['id'];
    }

    public static function getDetail()
    {
        return self::DETAIL['id'];
    }

    public static function getProperty()
    {
        return self::PROPERTY['id'];
    }

    public static function getPropertyDetail()
    {
        return self::PROPERTY_DETAIL['id'];
    }

    public static function getPropertyType()
    {
        return self::PROPERTY_TYPE['id'];
    }

    public static function getPropertyBooking()
    {
        return self::PROPERTY_BOOKING['id'];
    }

    public static function getPropertyBookingApproved()
    {
        return self::PROPERTY_BOOKING_APPROVED['id'];
    }

    public static function getPropertyBookingRejected()
    {
        return self::PROPERTY_BOOKING_REJECTED['id'];
    }

    public static function getPropertyBookingCancelled()
    {
        return self::PROPERTY_BOOKING_CANCELLED['id'];
    }

    public static function getPropertyBookingCompleted()
    {
        return self::PROPERTY_BOOKING_COMPLETED['id'];
    }

    public static function getAppBlocked()
    {
        return self::APP_BLOCKED['id'];
    }

    public static function getOwnerWithdrawMultiPropertyCommission()
    {
        return self::OWNER_WITHDRAW_MULTI_PROPERTY_COMMISSION['id'];
    }
    public static function getOwnerPropertyAddSaleAssistance()
    {
        return self::OWNER_PROPERTY_ADD_SALE_ASSISTANCE['id'];
    }
    public static function getAppUnblocked()
    {
        return self::APP_UNBLOCKED['id'];
    }
    public static function getAgencyConfirmedWithdrawnMultiPropertyCommission()
    {
        return self::AGENCY_CONFIRMED_WITHDRAWN_MULTI_PROPERTY_COMMISSION['id'];
    }
    public static function getAgencyRejectedWithdrawnMultiPropertyCommission()
    {
        return self::AGENCY_REJECTED_WITHDRAWN_MULTI_PROPERTY_COMMISSION['id'];
    }
    public static function getProductOrder()
    {
        return self::PRODUCT_ORDER['id'];
    }
    public static function getProductOrderApproved()
    {
        return self::PRODUCT_ORDER_APPROVED['id'];
    }
    public static function getProductOrderRejected()
    {
        return self::PRODUCT_ORDER_REJECTED['id'];
    }
    public static function getProductOrderCancelled()
    {
        return self::PRODUCT_ORDER_CANCELLED['id'];
    }
    public static function getOwnerWithdrawSinglePropertyCommission()
    {
        return self::OWNER_WITHDRAW_SINGLE_PROPERTY_COMMISSION['id'];
    }
    public static function getAgencyConfirmedWithdrawnSinglePropertyCommission()
    {
        return self::AGENCY_CONFIRMED_WITHDRAWN_SINGLE_PROPERTY_COMMISSION['id'];
    }
    public static function getAgencyRejectedWithdrawnSinglePropertyCommission()
    {
        return self::AGENCY_REJECTED_WITHDRAWN_SINGLE_PROPERTY_COMMISSION['id'];
    }
    public static function getCharityDonation()
    {
        return self::CHARITY_DONATION['id'];
    }
    public static function getCharityDonationApproved()
    {
        return self::CHARITY_DONATION_APPROVED['id'];
    }
    public static function getCharityDonationRejected()
    {
        return self::CHARITY_DONATION_REJECTED['id'];
    }
    public static function getAccommodationBooking()
    {
        return self::ACCOMMODATION_BOOKING['id'];
    }
    public static function getAccommodationReject()
    {
        return self::ACCOMMODATION_BOOKING_REJECT['id'];
    }
    public static function getAccommodationCancel()
    {
        return self::ACCOMMODATION_BOOKING_CANCEL['id'];
    }
    public static function getAccommodationBookingPayment()
    {
        return self::ACCOMMODATION_BOOKING_PAYMENT['id'];
    }
    public static function getAccommodationRejectPayment()
    {
        return self::ACCOMMODATION_BOOKING_REJECT_PAYMENT['id'];
    }
    public static function getAccommodationAuditingPayment()
    {
        return self::ACCOMMODATION_BOOKING_AUDITING_PAYMENT['id'];
    }
    public static function getAccommodationBookingApprove()
    {
        return self::ACCOMMODATION_BOOKING_APPROVE['id'];
    }
    public static function getMassageTherapistAdd()
    {
        return self::MASSAGE_THERAPIST_ADD['id'];
    }
    public static function getMassageTherapistApprove()
    {
        return self::MASSAGE_THERAPIST_APPROVE['id'];
    }
    public static function getMassageTherapistReject()
    {
        return self::MASSAGE_THERAPIST_REJECT['id'];
    }
    public static function getMassageShopBooking()
    {
        return self::MASSAGE_SHOP_BOOKING['id'];
    }
    public static function getMassageShopApprove()
    {
        return self::MASSAGE_SHOP_APPROVE['id'];
    }
    public static function getMassageShopReject()
    {
        return self::MASSAGE_SHOP_REJECT['id'];
    }
    public static function getMassageShopCancel()
    {
        return self::MASSAGE_SHOP_CANCEL['id'];
    }
    public static function getMassageShopPayment()
    {
        return self::MASSAGE_SHOP_PAYMENT['id'];
    }
    public static function getMassageShopRejectPayment()
    {
        return self::MASSAGE_SHOP_REJECT_PAYMENT['id'];
    }
    public static function getMassageShopAuditingPayment()
    {
        return self::MASSAGE_SHOP_AUDITING_PAYMENT['id'];
    }
    public static function getMassageShopForMassager()
    {
        return self::MASSAGE_SHOP_FOR_MASSAGER['id'];
    }
    public static function getAttractionBooking()
    {
        return self::ATTRACTION_BOOKING['id'];
    }
    public static function getAttractionApprove()
    {
        return self::ATTRACTION_APPROVE['id'];
    }
    public static function getAttractionReject()
    {
        return self::ATTRACTION_REJECT['id'];
    }
    public static function getAttractionCancel()
    {
        return self::ATTRACTION_CANCEL['id'];
    }
    public static function getLatestNews()
    {
        return self::LATEST_NEWS['id'];
    }
    public static function getPosterComment()
    {
        return self::POSTER_COMMENT['id'];
    }
    public static function getParticipantComment()
    {
        return self::PARTICIPANT_COMMENT['id'];
    }
    public static function getKtvGirlAdd()
    {
        return self::KTV_GIRL_ADD['id'];
    }
    public static function getKtvGirlApprove()
    {
        return self::KTV_GIRL_APPROVE['id'];
    }
    public static function getKtvGirlReject()
    {
        return self::KTV_GIRL_REJECT['id'];
    }
    public static function getKtvBooking()
    {
        return self::KTV_BOOKING['id'];
    }
    public static function getKtvApprove()
    {
        return self::KTV_APPROVE['id'];
    }
    public static function getKtvReject()
    {
        return self::KTV_REJECT['id'];
    }
    public static function getKtvCancel()
    {
        return self::KTV_CANCEL['id'];
    }
    public static function getKtvAddPayment()
    {
        return self::KTV_ADD_PAYMENT['id'];
    }
    public static function getKtvRejectPayment()
    {
        return self::KTV_REJECT_PAYMENT['id'];
    }
    public static function getKtvAuditingPayment()
    {
        return self::KTV_AUDITING_PAYMENT['id'];
    }
    public static function getKtvForKtvGirl()
    {
        return self::KTV_FOR_KTV_GIRL['id'];
    }
    public static function getShareBusinessPermission()
    {
        return self::SHARE_BUSINESS_PERMISSION['id'];
    }
    public static function getUpdateBusinessPermission()
    {
        return self::UPDATE_BUSINESS_PERMISSION['id'];
    }
    public static function getDeleteBusinessPermission()
    {
        return self::DELETE_BUSINESS_PERMISSION['id'];
    }
    public static function getBookingShopBusinessForUserShare()
    {
        return self::BOOKING_SHOP_BUSINESS_FOR_USER_SHARE['id'];
    }
    public static function getCancelShopBusinessForUserShare()
    {
        return self::CANCEL_SHOP_BUSINESS_FOR_USER_SHARE['id'];
    }
    public static function getMassageTherapistUpdate()
    {
        return self::MASSAGE_THERAPIST_UPDATE['id'];
    }
    public static function getKtvGirlUpdate()
    {
        return self::KTV_GIRL_UPDATE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            strtoupper(self::LINK['name']) => self::LINK['id'],
            strtoupper(self::DETAIL['name']) => self::DETAIL['id'],
            strtoupper(self::PROPERTY['name']) => self::PROPERTY['id'],
            strtoupper(self::PROPERTY_DETAIL['name']) => self::PROPERTY_DETAIL['id'],
            strtoupper(self::PROPERTY_TYPE['name']) => self::PROPERTY_TYPE['id'],
        ];
    }
}

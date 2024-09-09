<?php

namespace App\Helpers\Utils;

class ImagePath
{
    //Base Image Path
    const baseImagePath = 'images' .  DIRECTORY_SEPARATOR;

    /**
     * User Admin Image Path
     */
    const userAdminImagePath = self::baseImagePath . 'users';
    const userAdminThumbnailPath = self::baseImagePath . 'users' . DIRECTORY_SEPARATOR . 'thumbnail';

    /**
     * Media Image Path
     */
    const mediaImagePath = self::baseImagePath . 'media';

    /**
     * Notification Image Path
     */
    const notificationImagePath = self::baseImagePath . 'notification';

    /**
     * Business Type Image Path
     */
    const businessTypeImagePath = self::baseImagePath . 'business_type';

    /**
     * Province Image Path
     */
    const provinceImagePath = self::baseImagePath . 'province';

    /**
     * Flag Image Path
     */
    const flagImagePath = self::baseImagePath . 'flag_country';

    /**
     * Contact Image Path
     */
    const contactImagePath = self::baseImagePath . 'contact';
    const contactSignatureImagePath = self::contactImagePath . DIRECTORY_SEPARATOR . 'signature_image';

    /**
     * Contact Business Info
     */
    const contactBusinessInfoImagePath = self::baseImagePath . 'contact_business_info';

    /**
     * Banner Image Path
     */
    const bannerImagePath = self::baseImagePath . 'banner';

    /**
     * Bank Image Path
     */
    const bankImagePath = self::baseImagePath . 'bank';
    const bankQrCodeImagePath = self::baseImagePath . 'bank_qr_code';

    /**
     * Transaction Fee Slip Image Path
     */
    const transactionFeeSlipImagePath = self::baseImagePath . 'transaction_fee_slip';

    /**
     * Real Estate Image Path
     */
    const baseRealEstate = self::baseImagePath . 'real_estate' . DIRECTORY_SEPARATOR;
    //Property Base
    const baseProperty = self::baseRealEstate . 'property' . DIRECTORY_SEPARATOR;
    //Property Sub
    const propertyThumb = self::baseProperty . 'thumbnail';
    const propertyGallery = self::baseProperty . 'gallery';
    const propertyTelegram = self::baseProperty . 'telegram_qr_code';
    const propertyIDCard = self::baseProperty . 'id_card';
    const propertyRelatedDoc = self::baseProperty . 'related_document';
    const propertyBookingTransaction = self::baseProperty . 'booking_transaction';
    const propertyCommissionTransaction = self::baseProperty . 'commission_transaction';
    //Agency Base
    const baseRealEstateAgency = self::baseRealEstate . 'agency' . DIRECTORY_SEPARATOR;
    //Agency Sub
    const realEstateAgencyIDCard = self::baseRealEstateAgency . 'id_card';


    /**
     * Attraction Image Path
     */
    const baseAttraction = self::baseImagePath . 'attraction' . DIRECTORY_SEPARATOR;
    const attractionSocialContact = self::baseAttraction . 'social_contact';
    const attractionThumb = self::baseAttraction . 'thumbnail';
    const attractionGallery = self::baseAttraction . 'gallery';
    const attractionPlaceList = self::baseAttraction . 'price_list';
    const attractionTransaction = self::baseAttraction . 'attraction_transaction';


    /**
     * Shop Image Path
     */
    const baseShop = self::baseImagePath . 'shop' . DIRECTORY_SEPARATOR;
    const shopLogo = self::baseShop . 'logo';
    const shopCover = self::baseShop . 'cover';
    const shopCategory = self::baseShop . 'category';
    // Profile Base
    const baseProfile  = self::baseShop . 'profile' . DIRECTORY_SEPARATOR;
    // Profile Subs
    const shopProductBase = self::baseProfile . 'product' . DIRECTORY_SEPARATOR;
    const shopProductThumb = self::shopProductBase . 'thumbnail';
    const shopProductGallery = self::shopProductBase . 'gallery';
    const shopProductCategory = self::baseProfile . 'category';
    const shopProductSubCategory = self::baseProfile . 'sub_category';
    const shopProductBrand = self::baseProfile . 'brand';
    const shopProductModel = self::baseProfile . 'model';
    const shopProductOrderTransaction = self::baseProfile . 'shop_transaction';


    /**
     * Charity Image Path
     */
    const baseCharity = self::baseImagePath . 'charity' . DIRECTORY_SEPARATOR;
    const charityOrganizationLogo = self::baseCharity . 'organization_logo';
    const charityOrganizationGallery = self::baseCharity . 'organization_gallery';
    const charityTransaction = self::baseCharity . 'charity_transaction';
    const charityCategory = self::baseCharity . 'category';
    const charityRelatedDoc = self::baseCharity . 'related_document';


    /**
     * Accommodation Image Path
     */
    const baseAccommodation = self::baseImagePath . 'accommodation' . DIRECTORY_SEPARATOR;
    const accommodationLogo = self::baseAccommodation . 'logo';
    const accommodationCover = self::baseAccommodation . 'cover';
    const accommodationCategory = self::baseAccommodation . 'category';
    // Attribute
    const attributeImage = self::baseAccommodation . 'attribute_image';
    //Profile
    const accommodationRoomThumb = self::baseAccommodation. 'thumbnail';
    const accommodationRoomGallery = self::baseAccommodation. 'gallery';
    const accommodationTransaction = self::baseAccommodation. 'accommodation_transaction';


    /**
     * Massage Image Path
     */
    const baseMassage = self::baseImagePath . 'massage' . DIRECTORY_SEPARATOR;
    const massageLogo = self::baseMassage . 'logo';
    const massageCover = self::baseMassage . 'cover';

    // Massager Profile
    const massageServiceThumbnail = self::baseMassage . 'thumbnail';
    const massageServiceGallery = self::baseMassage . 'gallery_photo';
    const massageTransaction = self::baseMassage . 'massage_transaction';

    /**
     * Society Security
     */
    const baseSociety = self::baseImagePath . 'society' . DIRECTORY_SEPARATOR;

    //News Base
    const baseNewsImagePath = self::baseSociety . 'news' . DIRECTORY_SEPARATOR;
    //News Sub
    const newsThumbnail = self::baseNewsImagePath . 'thumbnail';
    const newsGallery = self::baseNewsImagePath . 'gallery_photo';
    const newsComment = self::baseNewsImagePath . 'comment';

    /**
     * KTV
     */
    const baseKTV = self::baseImagePath . 'ktv' . DIRECTORY_SEPARATOR;
    const ktvLogo = self::baseKTV . 'logo';
    const ktvCover = self::baseKTV . 'cover';
    const ktvTransaction = self::baseKTV . 'ktv_transaction';
    const ktvRelatedDoc = self::baseKTV . 'related_document';

    //Profile Base
    const baseKTVProfile = self::baseKTV . 'profile' . DIRECTORY_SEPARATOR;
    //Profile Sub
    const ktvProductCategory = self::baseKTVProfile . 'category';
    const baseKTVProduct = self::baseKTVProfile . 'product' . DIRECTORY_SEPARATOR;
    const ktvProductThumb = self::baseKTVProduct . 'thumbnail';
    const ktvProductGallery = self::baseKTVProduct . 'gallery';
    const baseKTVRoom = self::baseKTVProfile . 'room' . DIRECTORY_SEPARATOR;
    const ktvRoomThumb = self::baseKTVRoom . 'thumbnail';
    const ktvRoomGallery = self::baseKTVRoom . 'gallery';

    /**
     * Delivery
     */
    const baseDelivery = self::baseImagePath . 'delivery' . DIRECTORY_SEPARATOR;
    const vehicleDeliveryType = self::baseDelivery . 'vehicle';
    const itemType = self::baseDelivery . 'item_type';
    const itemGallery = self::baseDelivery . 'gallery';
}

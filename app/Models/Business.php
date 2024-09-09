<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\Types\IsOpen24Hour;
use App\Enums\Types\IsResizeImage;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessActive;
use App\Enums\Types\IsFreeDelivery;
use App\Enums\Types\AttributeStatus;
use App\Enums\Types\BusinessStatus;
use App\Enums\Types\IsBusinessOwner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\DocumentTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Types\PropertyAssetActive;
use App\Enums\Types\ContactHasPermission;
use App\Enums\Types\TransactionStatus;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'business';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const CONTACT_ID = 'contact_id';
    const COUNTRY_ID = 'country_id';
    const PROVINCE_ID = 'province_id';
    const DISTRICT_ID = 'district_id';
    const COMMUNE_ID = 'commune_id';
    const BUSINESS_CATEGORY_ID = 'business_category_id';
    const PROPERTY_TYPE_ID = 'property_type_id';
    const ACCOMMODATION_TYPE_ID = 'accommodation_type_id';
    const SALE_ASSISTANCE_ID = 'sale_assistance_id';
    const SALE_ASSISTANCE_COMMISSION = 'sale_assistance_commission';
    const SALE_ASSISTANCE_COMMISSION_TYPE = 'sale_assistance_commission_type';
    const AGENCY_COMMISSION = 'agency_commission';
    const AGENCY_COMMISSION_TYPE = 'agency_commission_type';
    const REF_AGENCY_COMMISSION = 'ref_agency_commission';
    const REF_AGENCY_COMMISSION_TYPE = 'ref_agency_commission_type';
    const CODE = 'code';
    const NAME = 'name';
    const IMAGE = 'image';
    const AUDIO = 'audio';
    const YOUTUBE_LINK = 'youtube_link';
    const VIDEO_LINK = 'video_link';
    const ADDRESS = 'address';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';
    const DESCRIPTION = 'description';
    const PAYMENT_POLICY = 'payment_policy';
    const PROJECT_DEVELOPMENT = 'project_development';
    const PHONE = 'phone';
    const TELEGRAM_NUMBER = 'telegram_number';
    const TELEGRAM_QR_CODE = 'telegram_qr_code';
    const EMAIL = 'email';
    const PRICE = 'price';
    const VIEW_COUNT = 'view_count';
    const RATE_COUNT = 'rate_count';
    const FACILITIES = 'facilities';
    const POLICY = 'policy';
    const FREE_DELIVERY = 'free_delivery';
    const DELIVERY_FEE = 'delivery_fee';
    const OPEN_24_HOUR = 'open_24_hour';
    const DISCOUNT_LABEL = 'discount_label';
    const OPEN_TIME = 'open_time';
    const CLOSE_TIME = 'close_time';
    const STATUS = 'status';
    const VERIFY = 'verify';
    const ACTIVE = 'active';
    const APP_FEE = 'app_fee';
    const DYNAMIC_LINK = 'dynamic_link';
    const WEBSITE_LINK = 'website_link';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        isset($data[self::COUNTRY_ID]) && $this->{self::COUNTRY_ID} = $data[self::COUNTRY_ID];
        isset($data[self::PROVINCE_ID]) && $this->{self::PROVINCE_ID} = $data[self::PROVINCE_ID];
        isset($data[self::DISTRICT_ID]) && $this->{self::DISTRICT_ID} = $data[self::DISTRICT_ID];
        isset($data[self::COMMUNE_ID]) && $this->{self::COMMUNE_ID} = $data[self::COMMUNE_ID];
        isset($data[self::BUSINESS_CATEGORY_ID]) && $this->{self::BUSINESS_CATEGORY_ID} = $data[self::BUSINESS_CATEGORY_ID];
        isset($data[self::PROPERTY_TYPE_ID]) && $this->{self::PROPERTY_TYPE_ID} = $data[self::PROPERTY_TYPE_ID];
        isset($data[self::ACCOMMODATION_TYPE_ID]) && $this->{self::ACCOMMODATION_TYPE_ID} = $data[self::ACCOMMODATION_TYPE_ID];
        isset($data[self::SALE_ASSISTANCE_COMMISSION]) && $this->{self::SALE_ASSISTANCE_COMMISSION} = $data[self::SALE_ASSISTANCE_COMMISSION];
        isset($data[self::SALE_ASSISTANCE_COMMISSION_TYPE]) && $this->{self::SALE_ASSISTANCE_COMMISSION_TYPE} = $data[self::SALE_ASSISTANCE_COMMISSION_TYPE];
        isset($data[self::AGENCY_COMMISSION]) && $this->{self::AGENCY_COMMISSION} = $data[self::AGENCY_COMMISSION];
        isset($data[self::AGENCY_COMMISSION_TYPE]) && $this->{self::AGENCY_COMMISSION_TYPE} = $data[self::AGENCY_COMMISSION_TYPE];
        isset($data[self::REF_AGENCY_COMMISSION]) && $this->{self::REF_AGENCY_COMMISSION} = $data[self::REF_AGENCY_COMMISSION];
        isset($data[self::REF_AGENCY_COMMISSION_TYPE]) && $this->{self::REF_AGENCY_COMMISSION_TYPE} = $data[self::REF_AGENCY_COMMISSION_TYPE];
        $this->{self::NAME} = $data[self::NAME];
        isset($data[self::YOUTUBE_LINK]) && $this->{self::YOUTUBE_LINK} = $data[self::YOUTUBE_LINK];
        isset($data[self::VIDEO_LINK]) && $this->{self::VIDEO_LINK} = $data[self::VIDEO_LINK];
        isset($data[self::ADDRESS]) && $this->{self::ADDRESS} = $data[self::ADDRESS];
        isset($data[self::LATITUDE]) && $this->{self::LATITUDE} = $data[self::LATITUDE];
        isset($data[self::LONGITUDE]) && $this->{self::LONGITUDE} = $data[self::LONGITUDE];
        isset($data[self::DESCRIPTION]) && $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
        isset($data[self::PAYMENT_POLICY]) && $this->{self::PAYMENT_POLICY} = $data[self::PAYMENT_POLICY];
        isset($data[self::PROJECT_DEVELOPMENT]) && $this->{self::PROJECT_DEVELOPMENT} = $data[self::PROJECT_DEVELOPMENT];
        isset($data[self::PHONE]) && $this->{self::PHONE} = $data[self::PHONE];
        isset($data[self::TELEGRAM_NUMBER]) && $this->{self::TELEGRAM_NUMBER} = $data[self::TELEGRAM_NUMBER];
        isset($data[self::EMAIL]) && $this->{self::EMAIL} = $data[self::EMAIL];
        isset($data[self::PRICE]) && $this->{self::PRICE} = $data[self::PRICE];
        isset($data[self::RATE_COUNT]) && $this->{self::RATE_COUNT} = $data[self::RATE_COUNT];
        isset($data[self::FACILITIES]) && $this->{self::FACILITIES} = $data[self::FACILITIES];
        isset($data[self::POLICY]) && $this->{self::POLICY} = $data[self::POLICY];
        isset($data[self::FREE_DELIVERY]) && $this->{self::FREE_DELIVERY} = $data[self::FREE_DELIVERY];
        isset($data[self::DELIVERY_FEE]) && $this->{self::DELIVERY_FEE} = $data[self::DELIVERY_FEE];
        isset($data[self::OPEN_24_HOUR]) && $this->{self::OPEN_24_HOUR} = $data[self::OPEN_24_HOUR];
        isset($data[self::DISCOUNT_LABEL]) && $this->{self::DISCOUNT_LABEL} = $data[self::DISCOUNT_LABEL];
        isset($data[self::OPEN_TIME]) && $this->{self::OPEN_TIME} = $data[self::OPEN_TIME];
        isset($data[self::CLOSE_TIME]) && $this->{self::CLOSE_TIME} = $data[self::CLOSE_TIME];
        isset($data[self::WEBSITE_LINK]) && $this->{self::WEBSITE_LINK} = $data[self::WEBSITE_LINK];
        isset($data[self::APP_FEE]) && $this->{self::APP_FEE} = $data[self::APP_FEE];
        isset($data[self::STATUS]) && $this->{self::STATUS} = $data[self::STATUS];
        isset($data[self::VERIFY]) && $this->{self::VERIFY} = $data[self::VERIFY];
        isset($data[self::ACTIVE]) && $this->{self::ACTIVE} = $data[self::ACTIVE];
    }

    /**
     * Get App Fee By Business ID
     */
    public static function getAppFeeByBusinessID($businessID)
    {
        $app_fee = 0;
        $data = Business::select(self::APP_FEE)->where(self::ID, $businessID)->first();
        if (!empty($data)) {
            $app_fee = $data->{self::APP_FEE};
        }

        return $app_fee;
    }


    /*
     * Relationship Area
     * */
    //Property Asset Relationship
    public function propertyAsset()
    {
        return $this->hasMany(PropertyAsset::class, PropertyAsset::BUSINESS_ID, Business::ID)
            ->join('asset_category', 'asset_category.id', 'property_asset.asset_category_id');
    }

    //Gallery Photo Relationship
    public function galleryPhoto()
    {
        return $this->hasMany(GalleryPhoto::class, GalleryPhoto::TYPE_ID, GalleryPhoto::ID);
    }

    //Related Document Relationship
    public function relatedDocument()
    {
        return $this->hasMany(RelatedDocument::class, RelatedDocument::BUSINESS_ID, RelatedDocument::ID)
            ->where(RelatedDocument::DOC_TYPE_ID, "!=", DocumentTypeEnum::getIDNo());
    }

    //Related Document (IDCard) Relationship
    public function idCardImage()
    {
        return $this->hasMany(RelatedDocument::class, RelatedDocument::BUSINESS_ID, RelatedDocument::ID)
            ->where(RelatedDocument::DOC_TYPE_ID, DocumentTypeEnum::getIDNo());
    }

    //Social Contact Relationship (Place Contact)
    public function placeSocialContact()
    {
        return $this->hasMany(PlaceContact::class, PlaceContact::BUSINESS_ID, self::ID);
    }

    //Place Video List Relationship
    public function placeVideoList()
    {
        return $this->hasMany(PlaceVideoList::class, PlaceVideoList::BUSINESS_ID, self::ID);
    }

    //Place Price List Relationship
    public function placePriceList()
    {
        return $this->hasMany(PlacePriceList::class, PlacePriceList::BUSINESS_ID, self::ID);
    }

    //Attraction Category
    public function CategoryList()
    {
        return $this->hasMany(Category::class, Category::BUSINESS_ID, self::ID);
    }

    //Business Bank Account Relationship
    public function businessBankAccount()
    {
        return $this->hasMany(BusinessBankAccount::class, BusinessBankAccount::BUSINESS_ID, Business::ID)
            ->join('bank_account', 'bank_account.id', 'business_bank_account.bank_account_id')
            ->join('bank', 'bank.id', 'bank_account.bank_id');
    }

    //Business Multi Category Relationship
    public function businessCategory()
    {
        return $this->hasMany(BusinessMultiCategory::class, BusinessMultiCategory::BUSINESS_ID, Business::ID)
            ->join('business_category', 'business_category.id', 'business_multi_category.business_category_id');
    }

    // Related Business with Business Attribute
    public function businessAttribute()
    {
        return $this->hasMany(BusinessAttribute::class, BusinessAttribute::BUSINESS_ID, self::ID)
            ->join('attribute', 'attribute.id', 'business_attribute.attribute_id');
    }

    //Business Work Day Relationship
    public function businessWorkDays()
    {
        return $this->hasMany(BusinessStaffWorkDays::class, BusinessStaffWorkDays::BUSINESS_ID, self::ID);
    }

    /*
     * List Area
     * */
    //List Property
    public static function listProperty($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $saleAssistanceID = isset($filter['sale_assistance_id']) ? $filter['sale_assistance_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $propertyId = isset($filter['property_id']) ? $filter['property_id'] : null;
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;
        $communeId = isset($filter['commune_id']) ? $filter['commune_id'] : null;
        $propertyTypeID = isset($filter['property_type_id']) ? $filter['property_type_id'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $verify = isset($filter['verify']) ? $filter['verify'] : null;
        $verify = $verify == "0" ? 2 : $verify;
        $active = isset($filter['show']) ? $filter['show'] : null;
        $active = $active == "0" ? 2 : $active;
        $status = isset($filter['status']) ? $filter['status'] : null;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Price Range
        $priceMin = isset($filter['price']['min']) ? floatval($filter['price']['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($filter['price']['max']) ? floatval($filter['price']['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $mostView = $sortBy == 'most_view' ? 'most_view' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;
        $priceAsc = $sortBy == 'price_asc' ? 'price_asc' : null;
        $priceDsc = $sortBy == 'price_desc' ? 'price_desc' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        //Sort Admin
        $sortCode = $sortBy == 'code' ? 'code' : null;
        $sortPrice = $sortBy == 'price' ? 'price' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        return self::select(
            'business.id',
            'business.business_type_id',
            'property_type.id as property_type_id',
            'property_type.name as property_type_name',
            'property_type.type as property_type',
            'business.contact_id',
            'contact.fullname as owner_name',
            'contact.profile_image as owner_profile',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            'commune.id as commune_id',
            DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as commune_name"),
            'business.code',
            'business.name',
            'business.image',
            'business.price',
            'business.youtube_link',
            'business.video_link',
            'business.phone',
            'business.telegram_number',
            'business.telegram_qr_code',
            'business.view_count',
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.description',
            'business.payment_policy',
            'business.project_development',
            'business.sale_assistance_id',
            'sale_assistance.fullname as sale_assistance_name',
            'sale_assistance.code as sale_assistance_code',
            'sale_assistance.profile_image as sale_assistance_profile',
            'business.sale_assistance_commission',
            'business.sale_assistance_commission_type',
            'business.agency_commission',
            'business.agency_commission_type',
            'business.ref_agency_commission',
            'business.ref_agency_commission_type',
            'business.status',
            'business.verify',
            'business.app_fee',
            'business.created_at'
        )
            ->join('property_type', 'property_type.id', 'business.property_type_id')
            ->join('province', 'province.id', 'business.province_id')
            ->join('district', 'district.id', 'business.district_id')
            ->join('commune', 'commune.id', 'business.commune_id')
            ->join('contact', 'contact.id', 'business.contact_id')
            ->leftjoin('contact as sale_assistance', 'sale_assistance.id', 'business.sale_assistance_id')
            //Filter From Mobile
            ->when($currentUserID, function ($query) use ($currentUserID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($currentUserID) {
                        $query->where('business.contact_id', $currentUserID)
                            ->orWhere(function ($query) use ($currentUserID) {
                                $query->where('business_share_contact.contact_id', $currentUserID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_PROPERTY);
                            });
                    });
            })
            //Filter From Mobile
            ->when($saleAssistanceID, function ($query) use ($saleAssistanceID) {
                $query->where('business.sale_assistance_id', $saleAssistanceID);
            })
            //Filter From Admin
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('contact.id', $businessOwnerID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('business.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($propertyId, function ($query) use ($propertyId) {
                $query->where('business.id', $propertyId);
            })
            ->when($propertyTypeID, function ($query) use ($propertyTypeID) {
                $query->where('property_type.id', $propertyTypeID);
            })
            ->when($countryId, function ($query) use ($countryId) {
                $query->where("business.country_id", $countryId);
            })
            ->when($provinceId, function ($query) use ($provinceId) {
                $query->where("province.id", $provinceId);
            })
            ->when($districtId, function ($query) use ($districtId) {
                $query->where("district.id", $districtId);
            })
            ->when($communeId, function ($query) use ($communeId) {
                $query->where("commune.id", $communeId);
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('business.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($newest, function ($query) {
                $query->orderBy("business.id", "DESC");
            })
            ->when($mostView, function ($query) {
                $query->orderBy("business.view_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when($priceAsc, function ($query) {
                $query->orderBy('business.price');
            })
            ->when($priceDsc, function ($query) {
                $query->orderBy('business.price', 'DESC');
            })
            ->when($priceMin, function ($query) use ($priceMin) {
                $query->where('business.price', '>=', $priceMin);
            })
            ->when($priceMax, function ($query) use ($priceMax) {
                $query->where('business.price', '<=', $priceMax);
            })
            ->when($sortCode, function ($query) use ($sortType) {
                $query->orderBy('business.code', $sortType);
            })
            ->when($sortPrice, function ($query) use ($sortType) {
                $query->orderBy('business.price', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('business.created_at', $sortType);
            })
            ->when(empty($isAdminRequest), function ($query) use ($mostLike) {
                $query->leftjoin(
                    DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getProperty() . "' ) favorite"),
                    function ($join) {
                        $join->on('business.id', '=', 'favorite.business_id');
                    }
                )
                    ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
                    ->where('business.active', BusinessActive::getTrue())
                    // ->where('contact.is_property_owner', IsBusinessOwner::getYes())
                    ->when($mostLike, function ($query) {
                        $query->orderBy("favorite.id", "DESC")
                            ->orderBy("business.id", "DESC");
                    });
            })
            ->when($orderCount, function ($query) {
                $query->leftjoin('transaction', 'business.id', 'transaction.business_id')
                    ->where(function ($query) {
                        $query->orderBy("count_business", "DESC")
                            ->orderBy("business.id", "DESC");
                        // ->where("transaction.status", TransactionStatus::getApproved());
                    });
            })
            ->when($verify, function ($query) use ($verify) {
                if ($verify == 2) {
                    $query->where('business.verify', BusinessActive::getFalse());
                } else {
                    $query->where('business.verify', $verify);
                }
            })
            ->when($active, function ($query) use ($active) {
                if ($active == 2) {
                    $query->where('business.active', BusinessActive::getFalse());
                } else {
                    $query->where('business.active', $active);
                }
            })
            ->when($status, function ($query) use ($status) {
                if ($status == BusinessStatus::getApproved()) {
                    $query->where('business.status', BusinessStatus::getApproved());
                } else if ($status == BusinessStatus::getBooking()) {
                    $query->where('business.status', BusinessStatus::getBooking());
                } else if ($status == BusinessStatus::getCompletedBooking()) {
                    $query->where('business.status', BusinessStatus::getCompletedBooking());
                }
            })
            ->whereNull('business.deleted_at')
            ->whereNotNull('business.property_type_id')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getProperty())
                        ->orderBy('gallery_photo.order', 'DESC');
                },
                'propertyAsset' => function ($query) {
                    $query->select(
                        'property_asset.*',
                        'asset_category.name as asset_category_name'
                    )
                        ->where('property_asset.active', PropertyAssetActive::getTrue())
                        ->orderBy('property_asset.id', 'DESC')
                        ->get();
                }
            ])
            ->groupBy('business.id')
            ->orderBy('business.id', 'asc');
    }

    //List Attraction
    public static function listAttraction($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $attractionID = isset($filter['attraction_id']) ? $filter['attraction_id'] : null;
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;
        $isDiscount = isset($filter['is_discount']) ? $filter['is_discount'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $verify = isset($filter['verify']) ? $filter['verify'] : null;
        $verify = $verify == "0" ? 2 : $verify;
        $active = isset($filter['status']) ? $filter['status'] : null;
        $active = $active == "0" ? 2 : $active;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $topRated = $sortBy == 'top_rated' ? 'top_rated' : null;
        $mostView = $sortBy == 'most_view' ? 'most_view' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        //Price Range
        $priceMin = isset($filter['price']['min']) ? floatval($filter['price']['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($filter['price']['max']) ? floatval($filter['price']['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort Admin
        $sortProvinceName = $sortBy == 'province_name' ? 'province_name' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        return self::select(
            'business.id',
            'business.business_type_id',
            'business.contact_id',
            'contact.fullname as contact_name',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            'business.name',
            'business.image',
            'business.price',
            'business.discount_label',
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.view_count',
            'business.rate_count',
            'business.status',
            'business.verify',
            'business.app_fee',
            'business.created_at',
            'business.description',
        )
            ->join('contact', 'contact.id', 'business.contact_id')
            ->join('province', 'province.id', 'business.province_id')
            ->leftjoin('district', 'district.id', 'business.district_id')
            ->when($attractionID, function ($query) use ($attractionID) {
                $query->where('business.id', $attractionID);
            })
            //Filter From Mobile
            ->when($currentUserID, function ($query) use ($currentUserID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($currentUserID) {
                        $query->where('business.contact_id', $currentUserID)
                            ->orWhere(function ($query) use ($currentUserID) {
                                $query->where('business_share_contact.contact_id', $currentUserID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_ATTRACTION);
                            });
                    });
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('business.contact_id', $businessOwnerID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($countryId, function ($query) use ($countryId) {
                $query->where("business.country_id", $countryId);
            })
            ->when($provinceId, function ($query) use ($provinceId) {
                $query->where("province.id", $provinceId);
            })
            ->when($districtId, function ($query) use ($districtId) {
                $query->where("district.id", $districtId);
            })
            ->when($isDiscount, function ($query) {
                $query->whereNotNull("business.discount_label");
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('business.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($newest, function ($query) {
                $query->orderBy("business.id", "DESC");
            })
            ->when($mostView, function ($query) {
                $query->orderBy("business.view_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when($topRated, function ($query) {
                $query->orderBy("business.rate_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when($sortProvinceName, function ($query) use ($sortType) {
                $query->orderBy(DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}')"), $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('business.created_at', $sortType);
            })
            ->when(empty($isAdminRequest), function ($query) use ($mostLike) {
                $query->leftjoin(
                    DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getAttraction() . "' ) favorite"),
                    function ($join) {
                        $join->on('business.id', '=', 'favorite.business_id');
                    }
                )
                    ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
                    ->where('business.active', BusinessActive::getTrue())
                    // ->where('contact.is_attraction_owner', IsBusinessOwner::getYes())
                    ->when($mostLike, function ($query) {
                        $query->orderBy("favorite.id", "DESC")
                            ->orderBy("business.id", "DESC");
                    });
            })
            ->when($orderCount, function ($query) {
                $query->leftjoin('transaction', 'business.id', 'transaction.business_id')
                    ->where(function ($query) {
                        $query->orderBy("count_business", "DESC")
                            ->orderBy("business.id", "DESC");
                        // ->where("transaction.status", TransactionStatus::getApproved());
                    });
            })
            ->when($priceMin, function ($query) use ($priceMin) {
                $query->where('business.price', '>=', $priceMin);
            })
            ->when($priceMax, function ($query) use ($priceMax) {
                $query->where('business.price', '<=', $priceMax);
            })
            ->when($verify, function ($query) use ($verify) {
                if ($verify == 2) {
                    $query->where('business.verify', BusinessActive::getFalse());
                } else {
                    $query->where('business.verify', $verify);
                }
            })
            ->when($active, function ($query) use ($active) {
                if ($active == 2) {
                    $query->where('business.active', BusinessActive::getFalse());
                } else {
                    $query->where('business.active', $active);
                }
            })
            ->where('business.business_type_id', BusinessTypeEnum::getAttraction())
            ->whereNull('business.deleted_at')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getAttraction())
                        ->orderBy('gallery_photo.order', 'DESC');
                },
                'placeSocialContact' => function ($query) {
                    $query->orderBy('place_contact.id', 'DESC');
                },
                'placeVideoList' => function ($query) {
                    $query->orderBy('place_video_list.id', 'DESC');
                },
            ])
            ->groupBy('business.id')
            ->orderBy('business.id', 'asc');
    }

    //List Shop
    public static function listShop($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $shopID = isset($filter['shop_id']) ? $filter['shop_id'] : null;
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $contactID = isset($filter['contact_id']) ? $filter['contact_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;
        $categoryID = isset($filter['category_id']) ? $filter['category_id'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $isDiscount = isset($filter['is_discount']) ? $filter['is_discount'] : null;
        $isFreeDelivery = isset($filter['is_free_delivery']) ? $filter['is_free_delivery'] : null;
        $isOpen24Hour = isset($filter['is_open_24_hour']) ? $filter['is_open_24_hour'] : null;
        $location = isset($filter['location']) ? $filter['location'] : null;
        $verify = isset($filter['verify']) ? $filter['verify'] : null;
        $verify = $verify == "0" ? 2 : $verify;
        $active = isset($filter['status']) ? $filter['status'] : null;
        $active = $active == "0" ? 2 : $active;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $topRated = $sortBy == 'top_rated' ? 'top_rated' : null;
        $mostView = $sortBy == 'most_view' ? 'most_view' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        // Sort Admin
        $sortCategoryName = $sortBy == 'category_name' ? 'category_name' : null;
        $sortBusinessName = $sortBy == 'name' ? 'name' : null;
        $sortAddress = $sortBy == 'address' ? 'address' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        return self::select(
            'business.id',
            'business_type.id as business_type_id',
            'business_type.name as business_type_name',
            'business.contact_id',
            'contact.fullname as contact_name',
            'business.name',
            'business.image',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.view_count',
            'business.rate_count',
            'business.status',
            'business.verify',
            'business.active as `show`',
            'business.created_at',
            'business.free_delivery',
            'business.delivery_fee',
            'business.open_24_hour',
            DB::raw('TIME(business.open_time) as open_time'),
            DB::raw('TIME(business.close_time) as close_time'),
            'business.discount_label',
            'business.description',
            'business.dynamic_link',
            'business.app_fee',
        )
            ->join('contact', 'contact.id', 'business.contact_id')
            ->leftjoin('province', 'province.id', 'business.province_id')
            ->leftjoin('district', 'district.id', 'business.district_id')
            ->join(
                'business_type',
                function ($join) {
                    $join->on('business_type.id', '=', 'business.business_type_id')
                        ->where(function ($query) {
                            $query->where('business_type.id', BusinessTypeEnum::getShopRetail())
                                ->orWhere('business_type.id', BusinessTypeEnum::getShopWholesale())
                                ->orWhere('business_type.id', BusinessTypeEnum::getRestaurant())
                                ->orWhere('business_type.id', BusinessTypeEnum::getShopLocalProduct())
                                ->orWhere('business_type.id', BusinessTypeEnum::getService())
                                ->orWhere('business_type.id', BusinessTypeEnum::getModernCommunity());
                        });
                }
            )
            ->join('business_multi_category', function ($join) use ($categoryID) {
                $join->on('business.id', '=', 'business_multi_category.business_id')
                ->when($categoryID, function ($query) use ($categoryID) {
                    $query->where('business_multi_category.business_category_id', $categoryID);
                });
            })
            ->when($shopID, function ($query) use ($shopID) {
                $query->where('business.id', $shopID);
            })
            //Filter From Admin
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('contact.id', $businessOwnerID);
            })
            //Filter From Mobile
            ->when($currentUserID, function ($query) use ($currentUserID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($currentUserID) {
                        $query->where('business.contact_id', $currentUserID)
                            ->orWhere(function ($query) use ($currentUserID) {
                                $query->where('business_share_contact.contact_id', $currentUserID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_SHOP);
                            });
                    });
            })
            ->when($contactID, function ($query) use ($contactID) {
                $query->where('transaction.customer_id', $contactID);
            })
            ->when($businessTypeID, function ($query) use ($businessTypeID) {
                $query->where('business.business_type_id', $businessTypeID);
            })
            ->when($countryId, function ($query) use ($countryId) {
                $query->where("business.country_id", $countryId);
            })
            ->when($provinceId, function ($query) use ($provinceId) {
                $query->where("province.id", $provinceId);
            })
            ->when($districtId, function ($query) use ($districtId) {
                $query->where("district.id", $districtId);
            })
            ->when($location, function ($query) use ($location) {
                $query->where('business.address', $location);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('business.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortBusinessName, function ($query) use ($sortType) {
                $query->orderBy('business_type.name', $sortType);
            })
            ->when($sortCategoryName, function ($query) use ($sortType) {
                $query->orderBy('business_category.name', $sortType);
            })
            ->when($sortAddress, function ($query) use ($sortType) {
                $query->orderBy('business.address', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('business.created_at', $sortType);
            })
            ->when($newest, function ($query) {
                $query->orderBy("business.id", "DESC");
            })
            ->when($topRated, function ($query) {
                $query->orderBy("business.rate_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when($mostView, function ($query) {
                $query->orderBy("business.view_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when(empty($isAdminRequest), function ($query) use ($mostLike, $businessTypeID) {
                $query->leftjoin(
                    DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . $businessTypeID . "' ) favorite"),
                    function ($join) {
                        $join->on('business.id', '=', 'favorite.business_id');
                    }
                )
                    ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
                    ->where('business.active', BusinessActive::getTrue())
                    // ->where('contact.is_seller', IsBusinessOwner::getYes())
                    ->when($mostLike, function ($query) {
                        $query->orderBy("favorite.id", "DESC")
                            ->orderBy("business.id", "DESC");
                    });
            })
            ->when(empty($isAdminRequest), function ($query) {
                $query->leftjoin(
                    DB::raw("(select * from business_staff_workdays) b "),
                    function ($join) {
                        $join->on('business.id', '=', 'b.business_id');
                    }
                )
                ->addSelect(
                    DB::raw('(CASE
                        WHEN (
                            (FIND_IN_SET(WEEKDAY(NOW()),
                                ( SELECT GROUP_CONCAT( DAY ) FROM business_staff_workdays WHERE business_id = `business`.`id` ))
                                OR b.DAY IS NULL
                            )
                            AND CheckOpenTime(business.open_time, business.close_time, TIME(NOW()))
                            AND business.open_24_hour = "' . IsOpen24Hour::getNo() . '"
                        ) THEN "open"
                        WHEN (
				            (FIND_IN_SET(WEEKDAY(NOW()),
                                ( SELECT GROUP_CONCAT( DAY ) FROM business_staff_workdays WHERE business_id = `business`.`id` ))
                                OR b.DAY IS NULL
                            ) AND business.open_24_hour = "' . IsOpen24Hour::getYes() . '"
				        ) THEN "open"
                        ELSE "close"
                    END) AS work_day')
                );
            })
            ->when($orderCount, function ($query) {
                $query->leftjoin('transaction', 'business.id', 'transaction.business_id')
                    ->where(function ($query) {
                        $query->orderBy("count_business", "DESC")
                            ->orderBy("business.id", "DESC");
                            // ->where("transaction.status", TransactionStatus::getApproved());
                    });
            })
            ->when($isDiscount, function ($query) {
                $query->whereNotNull("business.discount_label");
            })
            ->when($isFreeDelivery, function ($query) {
                $query->where("business.free_delivery", IsFreeDelivery::getYes());
            })
            ->when($isOpen24Hour, function ($query) use ($isOpen24Hour) {
                if($isOpen24Hour == IsOpen24Hour::getYes()) {
                    $query->where("business.open_24_hour", IsOpen24Hour::getYes());
                } else if ($isOpen24Hour == IsOpen24Hour::getNo()) {
                    $query->where("business.open_24_hour", IsOpen24Hour::getNo());
                }
            })
            ->when($verify, function ($query) use ($verify) {
                if ($verify == 2) {
                    $query->where('business.verify', BusinessActive::getFalse());
                } else {
                    $query->where('business.verify', $verify);
                }
            })
            ->when($active, function ($query) use ($active) {
                if ($active == 2) {
                    $query->where('business.active', BusinessActive::getFalse());
                } else {
                    $query->where('business.active', $active);
                }
            })
            ->whereNull('business.deleted_at')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getShopCover())
                        ->orderBy('gallery_photo.order', 'DESC');
                },
                'businessCategory' => function ($query) {
                    $query->select(
                        'business_multi_category.*',
                        'business_category.name as business_category_name',
                    )
                        ->orderBy('business_multi_category.id', 'DESC')
                        ->get();
                },
                'businessWorkDays' => function ($query) {
                    $query->orderBy('business_staff_workdays.day', 'asc');
                },
            ])
            ->groupBy('business.id')
            ->orderBy('business.id', 'asc');

    }

    //List Charity Organization
    public static function listCharityOrganization($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $organizationID = isset($filter['organization_id']) ? $filter['organization_id'] : null;
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $businessCategoryId = isset($filter['business_category_id']) ? $filter['business_category_id'] : null;
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $verify = isset($filter['verify']) ? $filter['verify'] : null;
        $verify = $verify == "0" ? 2 : $verify;
        $active = isset($filter['status']) ? $filter['status'] : null;
        $active = $active == "0" ? 2 : $active;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;

        // Sort Admin
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        return self::select(
            'business.id',
            'business.business_type_id',
            'business.business_category_id',
            'business_category.name as business_category_name',
            'business.contact_id',
            'contact.fullname as contact_name',
            'business.name',
            'business.phone',
            'business.image',
            'business.website_link',
            'business.video_link',
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.status',
            'business.verify',
            'business.created_at',
            'business.description',
            'business.app_fee',
        )
            ->join('contact', 'contact.id', 'business.contact_id')
            ->leftjoin('business_category', 'business_category.id', 'business.business_category_id')
            ->when($organizationID, function ($query) use ($organizationID) {
                $query->where('business.id', $organizationID);
            })
            ->when($currentUserID, function ($query) use ($currentUserID) {
                $query->where('business.contact_id', $currentUserID);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('business.contact_id', $businessOwnerID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($businessCategoryId, function ($query) use ($businessCategoryId) {
                $query->where("business_category.id", $businessCategoryId);
            })
            ->when($countryId, function ($query) use ($countryId) {
                $query->where("business.country_id", $countryId);
            })
            ->when($newest, function ($query) {
                $query->orderBy("business.id", "DESC");
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('business.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('business.created_at', $sortType);
            })
            ->when(empty($isAdminRequest), function ($query) use ($mostLike) {
                $query->leftjoin(
                    DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getCharityOrganization() . "' ) favorite"),
                    function ($join) {
                        $join->on('business.id', '=', 'favorite.business_id');
                    }
                )
                ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
                ->where('business.active', BusinessActive::getTrue())
                ->when($mostLike, function ($query) {
                    $query->orderBy("favorite.id", "DESC")
                        ->orderBy("business.id", "DESC");
                });
            })
            ->when($verify, function ($query) use ($verify) {
                if ($verify == 2) {
                    $query->where('business.verify', BusinessActive::getFalse());
                } else {
                    $query->where('business.verify', $verify);
                }
            })
            ->when($active, function ($query) use ($active) {
                if ($active == 2) {
                    $query->where('business.active', BusinessActive::getFalse());
                } else {
                    $query->where('business.active', $active);
                }
            })
            ->where('business.business_type_id', BusinessTypeEnum::getCharityOrganization())
            ->whereNull('business.deleted_at')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getCharityOrganization())
                        ->orderBy('gallery_photo.order', 'DESC');
                },
            ])
            ->groupBy('business.id')
            ->orderBy('business.id', 'asc');
    }

    // List Accommodation
    public static function listAccommodation($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $businessID = isset($filter['accommodation_id']) ? $filter['accommodation_id'] : null;
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;
        $businessCategoryId = isset($filter['business_category_id']) ? $filter['business_category_id'] : null;
        $orderStar = isset($filter['orderStar']) ? $filter['orderStar'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $verify = isset($filter['verify']) ? $filter['verify'] : null;
        $verify = $verify == "0" ? 2 : $verify;
        $active = isset($filter['status']) ? $filter['status'] : null;
        $active = $active == "0" ? 2 : $active;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Price Range
        $priceMin = isset($filter['price']['min']) ? floatval($filter['price']['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($filter['price']['max']) ? floatval($filter['price']['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $topRated = $sortBy == 'top_rated' ? 'top_rated' : null;
        $mostView = $sortBy == 'most_view' ? 'most_view' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        //sort Admin
        $sortPrice = $sortBy == 'price' ? 'price' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        return self::select(
            'business.id',
            'business_type.id as business_type_id',
            'business_type.name as business_type_name',
            'business.contact_id',
            'contact.fullname as contact_name',
            'business.name',
            'business_category.id as business_category_id',
            'business_category.name as business_category_name',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            'business.image',
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.price',
            'business.view_count',
            'business.rate_count',
            'business.status',
            'business.verify',
            'business.active as show',
            'business.created_at',
            'business.discount_label',
            'business.description',
            'business.policy',
            'business.video_link',
            'business.youtube_link',
            'business.app_fee',
            DB::raw('count(business.id) as count_business')
        )
        ->join('contact', 'contact.id', 'business.contact_id')
        ->join('province', 'province.id', 'business.province_id')
        ->leftjoin('district', 'district.id', 'business.district_id')
        ->join(
            'business_type',
            function ($join) {
                $join->on('business_type.id', '=', 'business.business_type_id')
                ->where('business_type.id', BusinessTypeEnum::getAccommodation());
            }
        )
        ->join('business_category', 'business_category.id', 'business.business_category_id')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('business.name', 'LIKE', '%' . $search . '%');
            });
        })
        //Filter From Mobile
        ->when($currentUserID, function ($query) use ($currentUserID) {
            /**
             * Example Raw SQL At Where Area
             * https://prnt.sc/z1rmrddA944f
             */
            $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                ->where(function ($query) use ($currentUserID) {
                    $query->where('business.contact_id', $currentUserID)
                        ->orWhere(function ($query) use ($currentUserID) {
                            $query->where('business_share_contact.contact_id', $currentUserID)
                                ->where('business_permission.action', BusinessPermission::VIEW_ACCOMMODATION);
                        });
                });
        })
        ->when($businessID, function ($query) use ($businessID) {
            $query->where('business.id', $businessID);
        })
        ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
            $query->where('business.contact_id', $businessOwnerID);
        })
        ->when($countryId, function ($query) use ($countryId) {
            $query->where("business.country_id", $countryId);
        })
        ->when($provinceId, function ($query) use ($provinceId) {
            $query->where("province.id", $provinceId);
        })
        ->when($districtId, function ($query) use ($districtId) {
            $query->where("district.id", $districtId);
        })
        ->when($orderStar, function ($query) use ($orderStar) {
            $query->where("business.rate_count", $orderStar);
        })
        ->when($businessCategoryId, function ($query) use ($businessCategoryId) {
            $query->where("business_category.id", $businessCategoryId);
        })
        ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
            $query->whereBetween('business.created_at', [$createdAtStartDate, $createdAtEndDate]);
        })
        ->when($newest, function ($query) {
            $query->orderBy("business.id", "DESC");
        })
        ->when($topRated, function ($query) {
            $query->orderBy("business.rate_count", "DESC")
                ->orderBy("business.id", "DESC");
        })
        ->when($mostView, function ($query) {
            $query->orderBy("business.view_count", "DESC")
                ->orderBy("business.id", "DESC");
        })
        ->when(empty($isAdminRequest), function ($query) use ($mostLike) {
            $query->leftjoin(
                DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getAccommodation() . "' ) favorite"),
                function ($join) {
                    $join->on('business.id', '=', 'favorite.business_id');
                }
            )
                ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
                ->where('business.active', BusinessActive::getTrue())
                // ->where('contact.is_hotel_owner', IsBusinessOwner::getYes())
                ->when($mostLike, function ($query) {
                    $query->orderBy("favorite.id", "DESC")
                        ->orderBy("business.id", "DESC");
                });
        })
        ->when($orderCount, function ($query) {
            $query->leftjoin('transaction', 'business.id', 'transaction.business_id')
                ->where(function ($query) {
                    $query->orderBy("count_business", "DESC")
                        ->orderBy("business.id", "DESC");
                        // ->where("transaction.status", TransactionStatus::getApproved());
                });
        })
        ->when($priceMin, function ($query) use ($priceMin) {
            $query->where('business.price', '>=', $priceMin);
        })
        ->when($priceMax, function ($query) use ($priceMax) {
            $query->where('business.price', '<=', $priceMax);
        })
        ->when($sortPrice, function ($query) use ($sortType) {
            $query->orderBy('business.price', $sortType);
        })
        ->when($sortCreatedAt, function ($query) use ($sortType) {
            $query->orderBy('business.created_at', $sortType);
        })
        ->when($verify, function ($query) use ($verify) {
            if ($verify == 2) {
                $query->where('business.verify', BusinessActive::getFalse());
            } else {
                $query->where('business.verify', $verify);
            }
        })
        ->when($active, function ($query) use ($active) {
            if ($active == 2) {
                $query->where('business.active', BusinessActive::getFalse());
            } else {
                $query->where('business.active', $active);
            }
        })
        ->whereNull('business.deleted_at')
        ->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getAccommodationCover())
                    ->orderBy('gallery_photo.order', 'DESC');
            },
            'businessAttribute' => function ($query) {
                $query->select(
                    'business_attribute.id',
                    'business_attribute.business_id',
                    'attribute.id as attribute_id',
                    'attribute.name',
                    'attribute.image',
                )
                    ->where('attribute.status', AttributeStatus::getEnabled())
                    ->orderBy('business_attribute.id', 'DESC')
                    ->get();
            }
        ])
        ->groupBy('business.id')
        ->orderBy('business.id', 'asc');
    }

    // List Massage
    public static function listMassage($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $massageID = isset($filter['massage_id']) ? $filter['massage_id'] : null;
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;
        $communeId = isset($filter['commune_id']) ? $filter['commune_id'] : null;
        $verify = isset($filter['verify']) ? $filter['verify'] : null;
        $verify = $verify == "0" ? 2 : $verify;
        $active = isset($filter['status']) ? $filter['status'] : null;
        $active = $active == "0" ? 2 : $active;
        $openTime = isset($filter['open_time']) ? $filter['open_time'] : null;
        $closeTime = isset($filter['close_time']) ? $filter['close_time'] : null;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $topRated = $sortBy == 'top_rated' ? 'top_rated' : null;
        $mostView = $sortBy == 'most_view' ? 'most_view' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        //Sort Admin
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortShow = $sortBy == 'show' ? 'show' : null;

        return self::select(
            'business.id',
            'business.business_type_id',
            'business_type.name as business_type_name',
            'business.contact_id',
            'contact.fullname as contact_name',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            'commune.id as commune_id',
            DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as commune_name"),
            'business.name',
            'business.phone',
            'business.description',
            'business.image',
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.price',
            'business.discount_label',
            DB::raw('TIME(business.open_time) as open_time'),
            DB::raw('TIME(business.close_time) as close_time'),
            'business.view_count',
            'business.rate_count',
            'business.status',
            'business.verify',
            'business.created_at',
            'business.video_link',
            'business.app_fee',
        )
        ->join('contact', 'contact.id', 'business.contact_id')
        ->join('province', 'province.id', 'business.province_id')
        ->join('district', 'district.id', 'business.district_id')
        ->join('commune', 'commune.id', 'business.commune_id')
        ->join(
            'business_type',
            function ($join) {
                $join->on('business_type.id', '=', 'business.business_type_id')
                    ->where('business_type.id', BusinessTypeEnum::getMassage());
            }
        )
        ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
            $query->where("contact.id", $businessOwnerID);
        })
        //Filter From Mobile
        ->when($currentUserID, function ($query) use ($currentUserID) {
            /**
             * Example Raw SQL At Where Area
             * https://prnt.sc/z1rmrddA944f
             */
            $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                ->where(function ($query) use ($currentUserID) {
                    $query->where('business.contact_id', $currentUserID)
                        ->orWhere(function ($query) use ($currentUserID) {
                            $query->where('business_share_contact.contact_id', $currentUserID)
                                ->where('business_permission.action', BusinessPermission::VIEW_MASSAGE_SHOP);
                        });
                });
        })
        ->when($massageID, function ($query) use ($massageID) {
            $query->where("business.id", $massageID);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('business.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($countryId, function ($query) use ($countryId) {
            $query->where("business.country_id", $countryId);
        })
        ->when($provinceId, function ($query) use ($provinceId) {
            $query->where("province.id", $provinceId);
        })
        ->when($districtId, function ($query) use ($districtId) {
            $query->where("district.id", $districtId);
        })
        ->when($communeId, function ($query) use ($communeId) {
            $query->where("commune.id", $communeId);
        })
        ->when($newest, function ($query) {
            $query->orderBy("business.id", "DESC");
        })
        ->when($topRated, function ($query) {
            $query->orderBy("business.rate_count", "DESC")
                ->orderBy("business.id", "DESC");
        })
        ->when($mostView, function ($query) {
            $query->orderBy("business.view_count", "DESC")
                ->orderBy("business.id", "DESC");
        })
        ->when(empty($isAdminRequest), function ($query) use ($mostLike) {
            $query->leftjoin(
                DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getMassage() . "' ) favorite"),
                function ($join) {
                    $join->on('business.id', '=', 'favorite.business_id');
                }
            )
                ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
                ->where('business.active', BusinessActive::getTrue())
                // ->where('contact.is_massage_owner', IsBusinessOwner::getYes())
                ->when($mostLike, function ($query) {
                    $query->orderBy("favorite.id", "DESC")
                        ->orderBy("business.id", "DESC");
                });
        })
        ->when($orderCount, function ($query) {
            $query->leftjoin('transaction', 'business.id', 'transaction.business_id')
                ->where(function ($query) {
                    $query->orderBy("count_business", "DESC")
                        ->orderBy("business.id", "DESC");
                        // ->where("transaction.status", TransactionStatus::getApproved());
                });
        })
        ->when($openTime, function ($query) use ($openTime) {
            $query->where('business.open_time','>=', $openTime);
        })
        ->when($closeTime, function ($query) use ($closeTime) {
            $query->where('business.close_time','<=', $closeTime);
        })
        ->when($verify, function ($query) use ($verify) {
            if ($verify == 2) {
                $query->where('business.verify', BusinessActive::getFalse());
            } else {
                $query->where('business.verify', $verify);
            }
        })
        ->when($active, function ($query) use ($active) {
            if ($active == 2) {
                $query->where('business.active', BusinessActive::getFalse());
            } else {
                $query->where('business.active', $active);
            }
        })
        ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
            $query->whereBetween('business.created_at', [$createdAtStartDate, $createdAtEndDate]);
        })
        ->when($sortName, function ($query) use ($sortType) {
            $query->orderBy('business.name', $sortType);
        })
        ->when($sortCreatedAt, function ($query) use ($sortType) {
            $query->orderBy('business.created_at', $sortType);
        })
        ->when($sortShow, function ($query) use ($sortType) {
            $query->orderBy('business.active', $sortType);
        })
        ->whereNull('business.deleted_at')
        ->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getMassageCover())
                    ->orderBy('gallery_photo.order', 'DESC');
            },
        ])
        ->groupBy('business.id')
        ->orderBy('business.id', 'asc');
    }

    //List KTV
    public static function listKTVs($filter = [], $sortBy = '', $sortType='desc')
    {
        //Filter
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;
        $orderStar = isset($filter['orderStar']) ? $filter['orderStar'] : null;
        $verify = isset($filter['verify']) ? $filter['verify'] : null;
        $verify = $verify == "0" ? 2 : $verify;
        $active = isset($filter['status']) ? $filter['status'] : null;
        $active = $active == "0" ? 2 : $active;
        $openTime = isset($filter['open_time']) ? $filter['open_time'] : null;
        $closeTime = isset($filter['close_time']) ? $filter['close_time'] : null;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $topRated = $sortBy == 'top_rated' ? 'top_rated' : null;
        $mostView = $sortBy == 'most_view' ? 'most_view' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        //Sort Admin
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortShow = $sortBy == 'show' ? 'show' : null;

        return self::join('contact', 'contact.id', 'business.contact_id')
            ->join('province', 'province.id', 'business.province_id')
            ->join('district', 'district.id', 'business.district_id')
            ->join(
                'business_type',
                function ($join) {
                    $join->on('business_type.id', '=', 'business.business_type_id')
                        ->where('business_type.id', BusinessTypeEnum::getKtv());
                }
            )
            ->select(
                'business.id',
                'business.business_type_id',
                'business_type.name as business_type_name',
                'business.contact_id',
                'contact.fullname as contact_name',
                'province.id as province_id',
                DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
                'district.id as district_id',
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
                'business.name',
                'business.phone',
                'business.description',
                'business.image',
                'business.latitude',
                'business.longitude',
                'business.address',
                'business.price',
                'business.discount_label',
                DB::raw('TIME(business.open_time) as open_time'),
                DB::raw('TIME(business.close_time) as close_time'),
                'business.view_count',
                'business.rate_count',
                'business.status',
                'business.verify',
                'business.created_at',
                'business.video_link',
                'business.app_fee',
            )
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where("contact.id", $businessOwnerID);
            })
            //Filter From Mobile
            ->when($currentUserID, function ($query) use ($currentUserID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($currentUserID) {
                        $query->where('business.contact_id', $currentUserID)
                            ->orWhere(function ($query) use ($currentUserID) {
                                $query->where('business_share_contact.contact_id', $currentUserID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_KTV);
                            });
                    });
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where("business.id", $businessID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($countryId, function ($query) use ($countryId) {
                $query->where("business.country_id", $countryId);
            })
            ->when($provinceId, function ($query) use ($provinceId) {
                $query->where("province.id", $provinceId);
            })
            ->when($districtId, function ($query) use ($districtId) {
                $query->where("district.id", $districtId);
            })
            ->when($newest, function ($query) {
                $query->orderBy("business.id", "DESC");
            })
            ->when($topRated, function ($query) {
                $query->orderBy("business.rate_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when($mostView, function ($query) {
                $query->orderBy("business.view_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when(empty($isAdminRequest), function ($query) use ($mostLike) {
                $query->leftjoin(
                    DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getKtv() . "' ) favorite"),
                    function ($join) {
                        $join->on('business.id', '=', 'favorite.business_id');
                    }
                )
                    ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
                    ->where('business.active', BusinessActive::getTrue())
                    // ->where('contact.is_ktv_owner', IsBusinessOwner::getYes())
                    ->when($mostLike, function ($query) {
                        $query->orderBy("favorite.id", "DESC")
                            ->orderBy("business.id", "DESC");
                    });
            })
            ->when($orderCount, function ($query) {
                $query->leftjoin('transaction', 'business.id', 'transaction.business_id')
                    ->where(function ($query) {
                        $query->orderBy("count_business", "DESC")
                            ->orderBy("business.id", "DESC");
                        // ->where("transaction.status", TransactionStatus::getApproved());
                    });
            })
            ->when($orderStar, function ($query) use ($orderStar) {
                $query->where("business.rate_count", $orderStar);
            })
            ->when($openTime, function ($query) use ($openTime) {
                $query->where('business.open_time', '>=', $openTime);
            })
            ->when($closeTime, function ($query) use ($closeTime) {
                $query->where('business.close_time', '<=', $closeTime);
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('business.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortName, function ($query) use ($sortType) {
                $query->orderBy('business.name', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('business.created_at', $sortType);
            })
            ->when($sortShow, function ($query) use ($sortType) {
                $query->orderBy('business.active', $sortType);
            })
            ->when($verify, function ($query) use ($verify) {
                if ($verify == 2) {
                    $query->where('business.verify', BusinessActive::getFalse());
                } else {
                    $query->where('business.verify', $verify);
                }
            })
            ->when($active, function ($query) use ($active) {
                if ($active == 2) {
                    $query->where('business.active', BusinessActive::getFalse());
                } else {
                    $query->where('business.active', $active);
                }
            })
            ->whereNull('business.deleted_at')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getKtvCover())
                        ->orderBy('gallery_photo.order', 'DESC');
                },
            ])
            ->groupBy('business.id')
            ->orderBy('business.id', 'desc');
    }
}

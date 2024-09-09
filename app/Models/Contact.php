<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Enums\Types\ContactStatus;
use App\Enums\Types\IsResizeImage;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\IsBusinessOwner;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Lumen\Auth\Authorizable;
use App\Enums\Types\BusinessTypeEnum;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Enums\Types\ContactHasPermission;
use App\Enums\Types\BankAccountContactType;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Types\BusinessTypeHasTransaction;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Contact extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;
    use SoftDeletes;

    const TABLE_NAME = 'contact';
    const ID = 'id';
    const CODE = 'code';
    const COUNTRY_ID = 'country_id';
    const PROVINCE_ID = 'province_id';
    const DISTRICT_ID = 'district_id';
    const IS_SELLER = 'is_seller';
    const IS_AGENCY = 'is_agency';
    const IS_PROPERTY_OWNER = 'is_property_owner';
    const IS_HOTEL_OWNER = 'is_hotel_owner';
    const IS_MASSAGE_OWNER = 'is_massage_owner';
    const IS_DRIVER = 'is_driver';
    const IS_NEWS = 'is_news';
    const IS_SALE_ASSISTANCE = 'is_sale_assistance';
    const IS_ATTRACTION_OWNER = 'is_attraction_owner';
    const IS_MASSAGER = 'is_massager';
    const IS_KTV_OWNER = 'is_ktv_owner';
    const IS_KTV_GIRL = 'is_ktv_girl';
    const FULLNAME = 'fullname';
    const PHONE = 'phone';
    const EMAIL = 'email';
    const GOOGLE = 'google';
    const SOCIAL_ID = 'social_id';
    const APPLE_ID = 'apple_id';
    const PASSWORD = 'password';
    const GENDER = 'gender';
    const AGENCY_PHONE = 'agency_phone';
    const ID_CARD = 'id_card';
    const PASSPORT_NO = 'passport_no';
    const ID_CARD_IMAGE_FRONT = 'id_card_image_front';
    const ID_CARD_IMAGE_BACK = 'id_card_image_back';
    const PROFILE_IMAGE = 'profile_image';
    const COVER_IMAGE = 'cover_image';
    const SIGNATURE_IMAGE = 'signature_image';
    const STATUS = 'status';
    const REFERRAL_AGENCY_ID = 'referral_agency_id';
    const POSITION_GROUP_ID = 'position_group_id';
    const VEHICLE_TYPE_ID = 'vehicle_type_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    //Get Contact ID
    public function getId()
    {
        return self::ID;
    }

    //Get Contact Name
    public function getName()
    {
        return self::FULLNAME;
    }

    //Check is activated
    public function isActivated()
    {
        return $this->{self::STATUS} == ContactStatus::getActivated();
    }

    //Check is not activate
    public function isNotActivate()
    {
        return $this->{self::STATUS} == ContactStatus::getNotActivate();
    }

    //Get By Phone
    public static function getByPhone($phone)
    {
        $data = Contact::where(self::PHONE,$phone)->first();
        return $data;
    }

    //Set Agency data
    public function setAgencyData($data)
    {
        $this->{self::IS_AGENCY} = IsBusinessOwner::getYes();
        $this->{self::FULLNAME} = $data[self::FULLNAME];
        $this->{self::GENDER} = $data[self::GENDER];
        $this->{self::AGENCY_PHONE} = $data[self::AGENCY_PHONE];
        $this->{self::ID_CARD} = $data[self::ID_CARD];
        $this->{self::PASSPORT_NO} = $data[self::PASSPORT_NO];
        !empty($data[self::REFERRAL_AGENCY_ID]) && $this->{self::REFERRAL_AGENCY_ID} = $data[self::REFERRAL_AGENCY_ID];
    }

    //Set Massager data
    public function setMassagerData($data)
    {
        $this->{self::IS_MASSAGER} = $data[self::IS_MASSAGER];
        $this->{self::FULLNAME} = $data[self::FULLNAME];
        $this->{self::GENDER} = $data[self::GENDER];
        $this->{self::AGENCY_PHONE} = $data[self::AGENCY_PHONE];
    }

    //Set Recipient data
    public function setRecipientData($data)
    {
        $this->{self::IS_NEWS} = IsBusinessOwner::getYes();
        $this->{self::FULLNAME} = $data[self::FULLNAME];
        // $this->{self::AGENCY_PHONE} = $data[self::AGENCY_PHONE];
        $this->{self::ID_CARD} = $data[self::ID_CARD];
        $this->{self::POSITION_GROUP_ID} = $data[self::POSITION_GROUP_ID];
        $this->{self::PROVINCE_ID} = $data[self::PROVINCE_ID];
        $this->{self::DISTRICT_ID} = $data[self::DISTRICT_ID];
    }

    //Set Ktv girl data
    public function setKtvGirlData($data)
    {
        $this->{self::FULLNAME} = $data[self::FULLNAME];
        $this->{self::GENDER} = $data[self::GENDER];
        $this->{self::IS_KTV_GIRL} = $data[self::IS_KTV_GIRL];
    }

    //Set Driver Data
    public function setDriverData($data)
    {
        $this->{self::FULLNAME} = $data[self::FULLNAME];
        $this->{self::ID_CARD} = $data[self::ID_CARD];
        $this->{self::IS_DRIVER} = $data[self::IS_DRIVER];
        $this->{self::VEHICLE_TYPE_ID} = $data[self::VEHICLE_TYPE_ID];
    }

    //Get Random Apple ID Name
    public static function getRandomAppleIdName()
    {
        $contact = self::orderBy('id', 'DESC')->select('id')->first();
        $id = 1;
        if (!empty($contact)) {
            $id = $contact->id + 1;
        }
        return "AppleID-" . str_pad($id, 5, 0, STR_PAD_LEFT);
    }

    //Get Random Phone Name
    public static function getRandomPhoneName()
    {
        $contact = self::orderBy('id', 'DESC')->select('id')->first();
        $id = 1;
        if (!empty($contact)) {
            $id = $contact->id + 1;
        }
        return "Phone-" . str_pad($id, 5, 0, STR_PAD_LEFT);
    }

    //Check has referral agency
    public function hasReferralAgency()
    {
        return !empty($this->{self::REFERRAL_AGENCY_ID});
    }

    //Check is sale assistance
    public function isSaleAssistance()
    {
        return $this->{self::IS_SALE_ASSISTANCE} == IsBusinessOwner::getYes();
    }

    //Check has bank account by business (Payment Method)
    public static function hasBankAccount($id, $businessType)
    {
        $has = false;
        $data = self::join('bank_account', function ($join) {
            $join->on('bank_account.contact_id', 'contact.id')
                ->where('bank_account.contact_type', BankAccountContactType::getContact())
                ->whereNull('bank_account.deleted_at');
        })
            ->join('business_bank_account', function ($join) use ($businessType) {
                $join->on('business_bank_account.bank_account_id', 'bank_account.id')
                    ->where('business_bank_account.business_type_id', $businessType)
                    ->whereNull('business_bank_account.deleted_at');
            })
            ->where('bank_account.contact_id', $id)
            ->groupBy('business_bank_account.bank_account_id')
            ->count();

        if ($data > 0) {
            $has = true;
        }

        return $has;
    }

    //Check has set share business permission
    public static function hasSetShareBusinessPermission($id)
    {
        $has = false;
        $data = BusinessShareContact::where('business_share_contact.contact_id', $id)->count();

        if ($data > 0) {
            $has = true;
        }

        return $has;
    }

    //Check permission when has set share business permission
    public static function checkHasShareBusinessPermission($id, $businessID, $action)
    {
        if (!empty($id)) {
            $hasPermission = ContactHasPermission::getNotSetPermission();
            if (self::hasSetShareBusinessPermission($id)) {
                $hasAllowPermissionData = BusinessShareContact::join('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->join('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where('business_share_contact.contact_id', $id)
                    ->when($businessID, function ($query) use ($businessID) {
                        $query->where('business_share_contact.business_id', $businessID);
                    })
                    ->where('business_permission.action', $action)
                    ->first();
                if (!empty($hasAllowPermissionData)) {
                    $hasPermission = ContactHasPermission::getYes();
                } else {
                    $hasPermission = ContactHasPermission::getNo();
                }
            }

            return $hasPermission;
        }

        return null;
    }

    //Get Combo List
    public static function getComboList()
    {
        return self::select('id', 'fullname', 'phone')->orderBy('id', 'DESC')->get();
    }

    //Contact Business Info List Relationship
    public function contactBusinessInfo()
    {
        return $this->hasMany(ContactBusinessInfo::class , ContactBusinessInfo::CONTACT_ID, self::ID);
    }

    //Get Current User
    public static function getCurrentUser($filter = [])
    {
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;

        return self::leftjoin('business_category as position_group', 'position_group.id', 'contact.position_group_id')
            ->leftjoin('business_category as vehicle_type', 'vehicle_type.id', 'contact.vehicle_type_id')
            ->select(
                'contact.id',
                'contact.code',
                'contact.country_id',
                'contact.province_id',
                'contact.district_id',
                'contact.is_seller',
                'contact.is_agency',
                'contact.is_attraction_owner',
                'contact.is_property_owner',
                'contact.is_hotel_owner',
                'contact.is_driver',
                'contact.is_news',
                'contact.is_sale_assistance',
                'contact.is_massage_owner',
                'contact.is_massager',
                'contact.is_ktv_owner',
                'contact.is_ktv_girl',
                'contact.fullname',
                'contact.phone',
                'contact.agency_phone',
                'contact.email',
                'contact.google',
                'contact.social_id',
                'contact.apple_id',
                'contact.gender',
                'contact.id_card',
                'contact.passport_no',
                'contact.id_card_image_front',
                'contact.id_card_image_back',
                'contact.profile_image',
                'contact.cover_image',
                'contact.signature_image',
                'contact.status',
                'contact.referral_agency_id',
                'position_group.id as position_group_id',
                'position_group.name as position_group_name',
                'vehicle_type.id as vehicle_type_id',
                'vehicle_type.name as vehicle_type_name',
            )
            ->where('contact.id', '=' , Auth::guard('mobile')->user()->id)
            ->with([
                'contactBusinessInfo' => function ($query) use ($businessTypeID) {
                    $query->select(
                        'contact_business_info.*',
                    )
                    ->when($businessTypeID, function ($query) use ($businessTypeID) {
                        $query->where('contact_business_info.business_type_id', $businessTypeID);
                    })
                    ->get();
                }
            ])
            ->first();
    }

    //List For Admin
    public static function listForAdmin($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = !empty($filter['search']) ? $filter['search'] : null;
        $type = isset($filter['type']) ? $filter['type'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;
        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');


        //Sort
        $sortType = !empty($sortType) ? $sortType : null;
        $sortFullName = $sortBy == 'fullname' ? 'fullname' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortStatus = $sortBy == 'status' ? 'status' : null;

        return self::when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('contact.fullname', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact.phone', 'LIKE', '%' . $search . '%');
            });
        })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('contact.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($type, function ($query) use ($type) {
                if($type == 1) {
                    $query->where('contact.is_property_owner', IsBusinessOwner::getYes());
                } else if ($type == 2) {
                    $query->where('contact.is_agency', IsBusinessOwner::getYes());
                } else if ($type == 3) {
                    $query->where('contact.is_sale_assistance', IsBusinessOwner::getYes());
                } else if ($type == 4) {
                    $query->where('contact.is_seller', IsBusinessOwner::getYes());
                } else if ($type == 5) {
                    $query->where('contact.is_hotel_owner', IsBusinessOwner::getYes());
                } else if ($type == 6) {
                    $query->where('contact.is_attraction_owner', IsBusinessOwner::getYes());
                } else if ($type == 7) {
                    $query->where('contact.is_massage_owner', IsBusinessOwner::getYes());
                } else if ($type == 8) {
                    $query->where('contact.is_massager', IsBusinessOwner::getYes());
                } else if ($type == 9) {
                    $query->where('contact.is_news', IsBusinessOwner::getYes());
                } else if ($type == 10) {
                    $query->where('contact.is_ktv_owner', IsBusinessOwner::getYes());
                } else if ($type == 11) {
                    $query->where('contact.is_ktv_girl', IsBusinessOwner::getYes());
                } else if ($type == 12) {
                    $query->where('contact.is_driver', IsBusinessOwner::getYes());
                }
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 2) {
                    $query->where('contact.status', ContactStatus::getNotActivate());
                } else {
                    $query->where('contact.status', $status);
                }
            })
            ->when($sortFullName, function ($query) use ($sortType) {
                $query->orderBy("contact.fullname", $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy("contact.created_at", $sortType);
            })
            ->when($sortStatus, function ($query) use ($sortType) {
                $query->orderBy("contact.status", $sortType);
            })
            ->select('contact.*');
    }

    //Get Contact Has Business List
    public static function getContactHasBusinessList($filter = [])
    {
        $businessType = isset($filter['business_type']) ? $filter['business_type'] : null;
        $businessHasTransaction = isset($filter['business_has_transaction']) ? $filter['business_has_transaction'] : null;

        return self::join('business', 'business.contact_id', 'contact.id')
            ->join('business_type', 'business_type.id', 'business.business_type_id')
            ->when($businessHasTransaction, function ($query) use ($businessHasTransaction) {
                $query->where('business_type.has_transaction', $businessHasTransaction);
            })
            ->when($businessType, function ($query) use ($businessType) {
                $query->where('business_type.id', $businessType);
            })
            ->select(
                'contact.id',
                'contact.fullname as name'
            )
            ->orderBy('contact.id')
            ->groupBy('business.contact_id')
            ->get();
    }

    //Get Agency List
    public static function getAgencyList($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;
        $referralID = isset($filter['referral_id']) ? $filter['referral_id'] : null;

        return self::select(
            'id',
            'fullname as name',
            'agency_phone as phone',
            'profile_image'
        )
            ->where('status', ContactStatus::getActivated())
            ->where('is_agency', IsBusinessOwner::getYes())
            ->when($referralID, function ($query) use ($referralID) {
                $query->where('referral_agency_id', $referralID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('fullname', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'DESC');
    }

    //Get Massager List
    public static function getMassagerList($filter = [], $contact_id = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::select(
            'id',
            'fullname as name',
            'gender',
            'agency_phone as phone',
            'profile_image'
        )
        ->where('status', ContactStatus::getActivated())
        ->where('is_massager', IsBusinessOwner::getYes())
        ->whereNotIn('id',$contact_id)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('fullname', 'LIKE', '%' . $search . '%');
            });
        })
        ->orderBy('id', 'DESC');
    }

    //Get News List
    public static function getRecipientList($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;
        $currentUserId = isset($filter['current_user_id']) ? $filter['current_user_id']: null;
        $positionGroupId = isset($filter['position_group_id']) ? $filter['position_group_id'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;

        return self::join('business_category', 'business_category.id', 'contact.position_group_id')
        ->join('province', 'province.id', 'contact.province_id')
        ->join('district', 'district.id', 'contact.district_id')
        ->leftjoin('contact_business_info', function ($join) {
            $join->on('contact_business_info.contact_id', '=', 'contact.id')
                ->where('contact_business_info.business_type_id', '=', BusinessTypeEnum::getNews());
        })
        ->select(
            'contact.id',
            'contact.fullname as name',
            'contact.code',
            'contact_business_info.phone as contact_business_info_phone',
            'contact_business_info.image as contact_business_info_image',
            'contact.id_card',
            'contact.id_card_image_front',
            'contact.id_card_image_back',
            'business_category.id as position_group_id',
            'business_category.name as position_group_name',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
        )
        ->where('contact.status', ContactStatus::getActivated())
        ->where('contact.is_news', IsBusinessOwner::getYes())
        ->when($currentUserId, function ($query) use ($currentUserId) {
            $query->where('contact.id', $currentUserId);
        })
        ->when($positionGroupId, function ($query) use ($positionGroupId) {
            $query->where('contact.position_group_id', $positionGroupId);
        })
        ->when($provinceId, function ($query) use ($provinceId) {
            $query->where('contact.province_id', $provinceId);
        })
        ->when($districtId, function ($query) use ($districtId) {
            $query->where('contact.district_id', $districtId);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('fullname', 'LIKE', '%' . $search . '%');
            });
        });
    }

    //Get KTV Girl List
    public static function getKtvGirlList($filter = [], $contact_id = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('contact_business_info', function ($join) {
                $join->on('contact_business_info.contact_id', '=', 'contact.id')
                    ->where('contact_business_info.business_type_id', '=', BusinessTypeEnum::getKtv());
            })
            ->select(
                'contact.id',
                'contact.fullname',
                'contact.gender',
                'contact_business_info.phone as ktv_girl_phone',
                'contact_business_info.image as ktv_girl_image',
                'contact.is_ktv_girl',
            )
            ->where('contact.status', ContactStatus::getActivated())
            ->where('contact.is_ktv_girl', IsBusinessOwner::getYes())
            ->whereNotIn('contact.id', $contact_id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('fullname', 'LIKE', '%' . $search . '%');
                });
            })
            ->groupBy('id')
            ->orderBy('id', 'DESC');;
    }

    //Get Driver List
    public static function getDriverList($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;
        $currentUserId = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $vehicleTypeId = isset($filter['vehicle_type_id']) ? $filter['vehicle_type_id'] : null;

        return self::join('business_category', 'business_category.id', 'contact.vehicle_type_id')
        ->leftjoin('contact_business_info', function ($join) {
            $join->on('contact_business_info.contact_id', '=', 'contact.id')
            ->where('contact_business_info.business_type_id', '=', BusinessTypeEnum::getDelivery());
        })
        ->select(
            'contact.id',
            'contact.fullname as name',
            'contact.code',
            'contact_business_info.phone as contact_business_info_phone',
            'contact_business_info.image as contact_business_info_image',
            'contact.id_card',
            'contact.id_card_image_front',
            'contact.id_card_image_back',
            'business_category.id as vehicle_type_id',
            'business_category.name as vehicle_type_name',
        )
        ->where('contact.status', ContactStatus::getActivated())
        ->where('contact.is_driver', IsBusinessOwner::getYes())
        ->when($currentUserId, function ($query) use ($currentUserId) {
            $query->where('contact.id', $currentUserId);
        })
        ->when($vehicleTypeId, function ($query) use ($vehicleTypeId) {
            $query->where('contact.vehicle_type_id', $vehicleTypeId);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('fullname', 'LIKE', '%' . $search . '%');
            });
        });
    }
}

<?php

namespace App\Models;

use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\ContactHasPermission;
use App\Enums\Types\IsResizeImage;
use App\Enums\Types\PropertyTypeEnum;
use App\Enums\Types\TransactionActive;
use App\Enums\Types\TransactionFeeStatus;
use App\Enums\Types\TransactionStatus;
use App\Helpers\StringHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Transaction extends Model
{
    const TABLE_NAME = 'transaction';
    const ID = 'id';
    const APP_TYPE_ID = 'app_type_id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const BUSINESS_ID = 'business_id';
    const BUSINESS_OWNER_ID = 'business_owner_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_PHONE = 'customer_phone';
    const CUSTOMER_ID_CARD = 'customer_id_card';
    const FULLNAME = 'fullname';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const BANK_ACCOUNT_ID = 'bank_account_id';
    const PROPERTY_ASSET_ID = 'property_asset_id';
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const TRANSACTION_DATE = 'transaction_date';
    const EXPIRE_DATE = 'expire_date';
    const CODE = 'code';
    const TOTAL_AMOUNT = 'total_amount';
    const SELL_AMOUNT = 'sell_amount';
    const QTY = 'qty';
    const VAT = 'vat';
    const IMAGE = 'image';
    const ACCOUNT_NUMBER = 'account_number';
    const ACCOUNT_NAME = 'account_name';
    const TRANSACTION_FEE = 'transaction_fee';
    const TRANSACTION_FEE_AMOUNT = 'transaction_fee_amount';
    const TRANSACTION_FEE_STATUS = 'transaction_fee_status';
    const STATUS = 'status';
    const ACTIVE = 'active';
    const CHECK_IN_DATE = 'check_in_date';
    const CHECK_OUT_DATE = 'check_out_date';
    const ORDER_TYPE = 'order_type';
    const PAYMENT_TYPE = 'payment_type';
    const TOTAL_TIP_AMOUNT = 'total_tip_amount';
    const REMARK = 'remark';
    const REMARK_BOOKING = 'remark_booking';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        //Set From Out: Image, Created At, Updated At
        $this->{self::APP_TYPE_ID} = $data[self::APP_TYPE_ID];
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::BUSINESS_OWNER_ID} = $data[self::BUSINESS_OWNER_ID];
        $this->{self::CUSTOMER_ID} = $data[self::CUSTOMER_ID];
        isset($data[self::CUSTOMER_NAME]) && $this->{self::CUSTOMER_NAME} = $data[self::CUSTOMER_NAME];
        isset($data[self::CUSTOMER_PHONE]) && $this->{self::CUSTOMER_PHONE} = $data[self::CUSTOMER_PHONE];
        isset($data[self::CUSTOMER_ID_CARD]) && $this->{self::CUSTOMER_ID_CARD} = $data[self::CUSTOMER_ID_CARD];
        isset($data[self::FULLNAME]) && $this->{self::FULLNAME} = $data[self::FULLNAME];
        isset($data[self::EMAIL]) && $this->{self::EMAIL} = $data[self::EMAIL];
        isset($data[self::PHONE]) && $this->{self::PHONE} = $data[self::PHONE];
        isset($data[self::BANK_ACCOUNT_ID]) && $this->{self::BANK_ACCOUNT_ID} = $data[self::BANK_ACCOUNT_ID];
        isset($data[self::PROPERTY_ASSET_ID]) && $this->{self::PROPERTY_ASSET_ID} = $data[self::PROPERTY_ASSET_ID];
        isset($data[self::SHIPPING_ADDRESS_ID]) && $this->{self::SHIPPING_ADDRESS_ID} = $data[self::SHIPPING_ADDRESS_ID];
        // $this->{self::TRANSACTION_DATE} = $data[self::TRANSACTION_DATE];
        isset($data[self::TRANSACTION_DATE]) && $this->{self::TRANSACTION_DATE} = $data[self::TRANSACTION_DATE];
        isset($data[self::EXPIRE_DATE]) && $this->{self::EXPIRE_DATE} = $data[self::EXPIRE_DATE];
        // $this->{self::CODE} = PrefixCode::getAutoCode(self::TABLE_NAME, PrefixCode::TRANSACTION);
        $this->{self::CODE} = $data[self::CODE];
        isset($data[self::TOTAL_AMOUNT]) && $this->{self::TOTAL_AMOUNT} = $data[self::TOTAL_AMOUNT];
        isset($data[self::SELL_AMOUNT]) && $this->{self::SELL_AMOUNT} = $data[self::SELL_AMOUNT];
        isset($data[self::VAT]) && $this->{self::VAT} = $data[self::VAT];
        isset($data[self::ACCOUNT_NUMBER]) && $this->{self::ACCOUNT_NUMBER} = $data[self::ACCOUNT_NUMBER];
        isset($data[self::ACCOUNT_NAME]) && $this->{self::ACCOUNT_NAME} = $data[self::ACCOUNT_NAME];
        isset($data[self::TRANSACTION_FEE]) && $this->{self::TRANSACTION_FEE} = $data[self::TRANSACTION_FEE];
        isset($data[self::TRANSACTION_FEE_AMOUNT]) && $this->{self::TRANSACTION_FEE_AMOUNT} = $data[self::TRANSACTION_FEE_AMOUNT];
        isset($data[self::CHECK_IN_DATE]) && $this->{self::CHECK_IN_DATE} = $data[self::CHECK_IN_DATE];
        isset($data[self::CHECK_OUT_DATE]) && $this->{self::CHECK_OUT_DATE} = $data[self::CHECK_OUT_DATE];
        isset($data[self::ORDER_TYPE]) && $this->{self::ORDER_TYPE} = $data[self::ORDER_TYPE];
        isset($data[self::PAYMENT_TYPE]) && $this->{self::PAYMENT_TYPE} = $data[self::PAYMENT_TYPE];
        isset($data[self::QTY]) && $this->{self::QTY} = $data[self::QTY];
        isset($data[self::TOTAL_TIP_AMOUNT]) && $this->{self::TOTAL_TIP_AMOUNT} = $data[self::TOTAL_TIP_AMOUNT];
        isset($data[self::REMARK]) && $this->{self::REMARK} = $data[self::REMARK];
        isset($data[self::REMARK_BOOKING]) && $this->{self::REMARK_BOOKING} = $data[self::REMARK_BOOKING];
    }

    //Set Status
    public static function setStatus($id, $status)
    {
        $transaction = self::find($id);
        if (!empty($transaction)) {
            $transaction->{self::STATUS} = $status;
            $transaction->{self::UPDATED_AT} = Carbon::now();
            $transaction->save();

            return true;
        }

        return false;
    }

    /*
     * Relationship Area
     * */
    //Product Order List Relationship
    public function productOrderList()
    {
        return $this->hasMany(ProductOrderList::class, ProductOrderList::TRANSACTION_ID, self::ID);
    }
    //Book List Relationship
    public function bookList()
    {
        return $this->hasMany(BookList::class, BookList::TRANSACTION_ID, self::ID)
        ->leftjoin('room', 'room.id', 'book_list.room_id');
    }
    //Transaction Contact List Relationship
    public function transactionContactList()
    {
        return $this->hasMany(TransactionContact::class, TransactionContact::TRANSACTION_ID, self::ID);
    }
    //Transaction Contact Detail List Relationship
    public function transactionContactDetailList()
    {
        return $this->hasMany(TransactionContactDetail::class, TransactionContactDetail::TRANSACTION_ID, self::ID);
    }

    //List Transaction Fee Admin (Use only admin)
    public static function listTransactionFeeAdmin($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $businessTypeId = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Sort
        $sortType = !empty($sortType) ? $sortType : null;
        $sortTransactionCount = $sortBy == 'transaction_count' ? 'transaction_count' : null;
        $sortGrandTotalAmount = $sortBy == 'transaction_fee_amount' ? 'transaction_fee_amount' : null;
        $sortOutstandingAmount = $sortBy == 'transaction_fee_outstanding_amount' ? 'transaction_fee_outstanding_amount' : null;
        $sortPaidAmount = $sortBy == 'transaction_paid_amount' ? 'transaction_paid_amount' : null;

        return DB::table('transaction', 'tst')
            ->select(
                'ct_gb_bt.*',
                DB::raw('COUNT(tst.id) AS transaction_count'),
                DB::raw('SUM(tst.transaction_fee_amount ) AS transaction_fee_amount'),
                DB::raw('SUM(CASE WHEN tst.transaction_fee_status = 1 THEN tst.transaction_fee_amount ELSE 0 END ) AS transaction_fee_paid_amount'),
                DB::raw('SUM(CASE WHEN tst.transaction_fee_status = 0 THEN tst.transaction_fee_amount ELSE 0 END ) AS transaction_fee_outstanding_amount')
            )
            ->join(
                DB::raw('(
                SELECT
                    ct.id AS business_owner_id,
                    ct.fullname AS business_owner_name,
                    bs.business_type_id,
                    bt.name AS business_type_name
                FROM
                    contact ct
                    JOIN business bs ON ct.id = bs.contact_id
                    JOIN business_type bt ON bt.id = bs.business_type_id
                GROUP BY
                    bs.business_type_id,
                    bs.contact_id
                ) ct_gb_bt'),
                function ($join) {
                    $join->on('ct_gb_bt.business_owner_id', '=', 'tst.business_owner_id')
                        ->on('ct_gb_bt.business_type_id', '=', 'tst.business_type_id');
                }
            )
            ->whereNotNull('tst.transaction_fee_amount')
            ->where('tst.transaction_fee_amount', '>', 0)
            ->when($businessTypeId, function ($query) use ($businessTypeId) {
                $query->where('tst.business_type_id', $businessTypeId);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('tst.business_owner_id', $businessOwnerID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('ct_gb_bt.business_owner_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('ct_gb_bt.business_type_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($sortTransactionCount, function ($query) use ($sortType) {
                $query->orderBy('transaction_count', $sortType);
            })
            ->when($sortGrandTotalAmount, function ($query) use ($sortType) {
                $query->orderBy('transaction_fee_amount', $sortType);
            })
            ->when($sortOutstandingAmount, function ($query) use ($sortType) {
                $query->orderBy('transaction_fee_outstanding_amount', $sortType);
            })
            ->when($sortPaidAmount, function ($query) use ($sortType) {
                $query->orderBy('transaction_paid_amount', $sortType);
            })
            ->when(empty($sortBy), function ($query) {
                $query->orderBy('tst.id', 'desc');
            })
            ->groupBy('ct_gb_bt.business_type_id', 'ct_gb_bt.business_owner_id');
    }

    //List Transaction Fee Common (Can use everywhere)
    public static function listTransactionFeeCommon($filter = [])
    {
        //Filter
        $businessTypeId = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;
        $groupByMonth = isset($filter['group_by_month']) ? $filter['group_by_month'] : true;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $createdDateByMonthYear = isset($filter['created_date_by_month_year']) ? $filter['created_date_by_month_year'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;
        $isMobileRequest = isset($filter['is_mobile_request']) ? $filter['is_mobile_request'] : null;

        return self::join('business_type', 'business_type.id', 'transaction.business_type_id')
            ->leftjoin('contact', 'contact.id', 'transaction.business_owner_id')
            ->select(
                'transaction.id',
                'transaction.business_type_id',
                'business_type.name as business_type_name',
                'contact.id as business_owner_id',
                'contact.fullname as business_owner_name',
                DB::raw('DATE_FORMAT(transaction.created_at, "%M, %Y") AS indebted_date'),
                'transaction.transaction_fee_status AS status',
            )
            ->where('transaction.status', TransactionStatus::getApproved())
            ->whereNotNull('transaction.transaction_fee_amount')
            ->where('transaction.transaction_fee_amount', '>', 0)
            ->when(empty($isAdminRequest), function ($query) {
                $query->whereRaw("DATE_FORMAT(transaction.created_at, '%Y-%m') < '" . Carbon::today()->format('Y-m') . "'");
            })
            ->when($businessTypeId, function ($query) use ($businessTypeId) {
                $query->where('transaction.business_type_id', $businessTypeId);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('transaction.business_owner_id', $businessOwnerID);
            })
            ->when($status, function ($query) use ($status, $isMobileRequest) {
                //2 = 0 if status = 0 eloquent when not fire
                if ($status == 2) {
                    $query->where('transaction.transaction_fee_status', TransactionFeeStatus::getBusinessNotYetPay())
                        ->when(!empty($isMobileRequest), function ($subQuery) {
                            $subQuery->whereRaw("DATE_FORMAT(transaction.created_at, '%Y-%m') != '" . Carbon::today()->format('Y-m') . "'");
                        });
                } else if ($status == TransactionFeeStatus::getBusinessPaid()) {
                    $query->where('transaction.transaction_fee_status', TransactionFeeStatus::getBusinessPaid());
                }
            })
            ->when(!empty($groupByMonth), function ($query) {
                $query->addSelect(
                    DB::raw('COUNT(*) AS total_count'),
                    DB::raw('SUM(transaction.transaction_fee_amount) AS total_amount'),
                )
                ->groupBy(DB::raw('MONTH(transaction.created_at)'))
                ->groupBy(DB::raw('YEAR(transaction.created_at)'));
            })
            ->when(empty($groupByMonth), function ($query) {
                $query->addSelect(
                    DB::raw('1 AS total_count'),
                    'transaction.transaction_fee_amount AS total_amount',
                );
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business_type.name', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('DATE_FORMAT(transaction.created_at, "%M, %Y")'), 'LIKE', '%' . $search . '%')
                        ->orWhere('transaction.transaction_fee_amount', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($createdDateByMonthYear, function ($query) use ($createdDateByMonthYear) {
                $query->whereRaw("DATE_FORMAT(transaction.created_at, '%M, %Y') = '" . $createdDateByMonthYear . "'");
            })
            ->orderBy('transaction.id', 'desc');
    }

    //List Property and Asset (Can use everywhere)
    public static function listPropertyAndAsset($filter = [], $sort = '')
    {
        //Filter
        $transactionId = isset($filter['sale_id']) ? $filter['sale_id'] : null;
        $createdDateByMonthYear = isset($filter['created_date_by_month_year']) ? $filter['created_date_by_month_year'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $agencyID = isset($filter['agency_id']) ? $filter['agency_id'] : null;
        $saleAssistanceID = isset($filter['sale_assistance_id']) ? $filter['sale_assistance_id'] : null;
        $propertyTypeID = isset($filter['property_type_id']) ? $filter['property_type_id'] : null;
        $provinceId = isset($filter['province_id']) ? $filter['province_id'] : null;
        $districtId = isset($filter['district_id']) ? $filter['district_id'] : null;
        $communeId = isset($filter['commune_id']) ? $filter['commune_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;

        //Price Rang
        $priceMin = isset($filter['price']['min']) ? floatval($filter['price']['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($filter['price']['max']) ? floatval($filter['price']['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort
        $newest = $sort == 'newest' ? 'newest' : null;
        $mostView = $sort == 'most_view' ? 'most_view' : null;
        $mostLike = $sort == 'most_like' ? 'most_like' : null;
        $priceAsc = $sort == 'price_asc' ? 'price_asc' : null;
        $priceDsc = $sort == 'price_desc' ? 'price_desc' : null;

        return self::join('business', 'business.id', 'transaction.business_id')
            ->join('property_type', 'property_type.id', 'business.property_type_id')
            ->leftjoin('province', 'province.id', 'business.province_id')
            ->leftjoin('district', 'district.id', 'business.district_id')
            ->leftjoin('commune', 'commune.id', 'business.commune_id')
            ->leftjoin('contact', 'contact.id', 'transaction.customer_id')
            ->leftjoin('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
            ->leftjoin('property_asset', 'property_asset.id', 'transaction.property_asset_id')
            ->leftjoin('asset_category', 'asset_category.id', 'property_asset.asset_category_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->where('transaction.business_type_id', BusinessTypeEnum::getProperty())
            ->when($transactionId, function ($query) use ($transactionId) {
                $query->where('transaction.id', $transactionId);
            })
            ->when($createdDateByMonthYear, function ($query) use ($createdDateByMonthYear) {
                $query->whereRaw("DATE_FORMAT(transaction.created_at, '%M, %Y') = '" . $createdDateByMonthYear . "'");
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($businessOwnerID) {
                        $query->where('transaction.business_owner_id', $businessOwnerID)
                            ->orWhere(function ($query) use ($businessOwnerID) {
                                $query->where('business_share_contact.contact_id', $businessOwnerID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_SALE_LIST_PROPERTY);
                            });
                    });
            })
            ->when($agencyID, function ($query) use ($agencyID) {
                $query->where('transaction.customer_id', $agencyID);
            })
            ->when($saleAssistanceID, function ($query) use ($saleAssistanceID) {
                $query->where('business.sale_assistance_id', $saleAssistanceID);
            })
            ->when($propertyTypeID, function ($query) use ($propertyTypeID) {
                $query->where('property_type.id', $propertyTypeID);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('transaction.status', $status);
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
            ->when($priceMin, function ($query) use ($priceMin) {
                $query->where('transaction.total_amount', ' >= ', $priceMin);
            })
            ->when($priceMax, function ($query) use ($priceMax) {
                $query->where('transaction.total_amount', ' <= ', $priceMax);
            })
            ->when($newest, function ($query) {
                $query->orderBy("transaction.id", "DESC");
            })
            ->when($mostView, function ($query) {
                $query->orderBy("business.view_count", "DESC")
                    ->orderBy("business.id", "DESC");
            })
            ->when($priceAsc, function ($query) {
                $query->orderBy('transaction.total_amount');
            })
            ->when($priceDsc, function ($query) {
                $query->orderBy('transaction.total_amount', 'DESC');
            })
            ->select(
                'transaction.id',
                'property_type.type as property_type',
                'transaction.business_type_id',
                'transaction.business_owner_id',
                'business_owner.fullname as business_owner_name',
                'business_owner.signature_image as business_owner_signature_image',
                'transaction.created_at as booking_date',
                'business.id as business_id',
                'business.image as business_image',
                // DB::raw("CASE
                // WHEN property_type.type = '" . PropertyTypeEnum::getSingle() . "'
                // THEN business.name
                // ELSE
                // CONCAT(business.name,' - ',property_asset.code)
                // END name
                // "),
                'business.name as name',
                DB::raw("CASE
                WHEN property_type.type = '" . PropertyTypeEnum::getSingle() . "'
                THEN business.code
                ELSE
                property_asset.code
                END property_code
                "),
                DB::raw("CASE
                WHEN property_type.type = '" . PropertyTypeEnum::getSingle() . "'
                THEN property_type.name
                ELSE
                asset_category.name
                END type
                "),
                DB::raw("CASE
                WHEN property_type.type = '" . PropertyTypeEnum::getSingle() . "'
                THEN business.image
                ELSE
                property_asset.image
                END thumbnail
                "),
                DB::raw("CASE
                WHEN property_type.type = '" . PropertyTypeEnum::getSingle() . "'
                THEN business.price
                ELSE
                property_asset.price
                END total_price
                "),
                'property_asset.size as asset_size',
                'transaction.customer_id as agency_id',
                'contact.fullname as agency_name',
                'contact.agency_phone',
                'transaction.customer_name',
                'transaction.customer_phone',
                'transaction.customer_id_card',
                'bank_account.id as bank_account_id',
                'transaction.account_number',
                'transaction.account_name',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'transaction.transaction_date',
                'transaction.total_amount as transaction_amount',
                'transaction.sell_amount',
                'transaction.transaction_fee_amount as app_fee_amount',
                'transaction.image as transaction_image',
                'transaction.remark',
                'transaction.status',
                'business.address as property_location',
                DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as property_province"),
                'district.id as district_id',
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as property_district"),
                'commune.id as commune_id',
                DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as property_commune"),
                'business.phone as property_phone',
                'transaction.code as transaction_code',
                'transaction.expire_date'
            )
            ->groupBy('transaction.id')
            ->orderBy('transaction.id', 'desc');
    }

    //Get Transaction (Use only Admin)
    public static function listPropertyTransactionAdmin($filter = [], $sortBy = '', $sortType = 'desc')
    {

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        // sort
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        return self::join('business', 'business.id', 'transaction.business_id')
            ->join('contact', 'contact.id', 'transaction.customer_id')
            ->join('property_type', 'property_type.id', 'business.property_type_id')
            ->leftjoin('property_asset', 'property_asset.id', 'transaction.property_asset_id')
            ->leftjoin('asset_category', 'asset_category.id', 'property_asset.asset_category_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('transaction.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('transaction.created_at', $sortType);
            })
            ->where('transaction.business_type_id', BusinessTypeEnum::getProperty())
            ->select(
                'transaction.id',
                'transaction.code',
                'property_type.type as property_type',
                'transaction.account_number as bank_account',
                'transaction.account_name',
                'bank.name as bank_name',
                'contact.fullname as agency_name',
                'contact.agency_phone',
                DB::raw("CASE
                WHEN property_type.type = '" . PropertyTypeEnum::getSingle() . "'
                THEN business.name
                ELSE
                CONCAT(business.name,' - ',property_asset.code)
                END name
            "),
                DB::raw("CASE
                WHEN property_type.type = '" . PropertyTypeEnum::getSingle() . "'
                THEN business.price
                ELSE
                property_asset.price
                END price
            "),
                'transaction.total_amount as deposit_amount',
                'transaction.sell_amount',
                'transaction.status',
                'property_asset.size as asset_size',
                'transaction.transaction_date',
                'transaction.expire_date',
                'transaction.created_at'
            )
            ->groupBy('transaction.id')
            ->orderBy('transaction.id', 'desc');
    }

    //List Shop (Can use everywhere)
    public static function listShop($filter = [], $sort = '')
    {
        //Filter
        $transactionID = isset($filter['sale_id']) ? $filter['sale_id'] : null;
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $customerID = isset($filter['customer_id']) ? $filter['customer_id'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $createdDateByMonthYear = isset($filter['created_date_by_month_year']) ? $filter['created_date_by_month_year'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;

        //Date Range
        $dateRange = isset($filter['date_range']) ? $filter['date_range'] : null;
        $startDate = empty($dateRange['start_date']) ? null : Carbon::parse($dateRange['start_date'])->format('Y-m-d');
        $endDate = empty($dateRange['end_date']) ? null : Carbon::parse($dateRange['end_date'])->format('Y-m-d');

        //Sort
        $sortShop = $sort == 'shop' ? 'shop' : null;
        $sortRecentOrder = $sort == 'recent_order' ? 'recent_order' : null;
        $sortTotalAmount = $sort == 'total_amount' ? 'total_amount' : null;
        $sortQty = $sort == 'qty' ? 'qty' : null;

        return self::join('product_order_list', 'product_order_list.transaction_id', 'transaction.id')
            ->join('business', 'business.id', 'transaction.business_id')
            ->join(
                'business_type',
                function ($join) {
                    $join->on('business_type.id', '=', 'transaction.business_type_id')
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
            ->join('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
            ->join('contact as customer', 'customer.id', 'transaction.customer_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->leftjoin('shipping_address', 'shipping_address.id', 'transaction.shipping_address_id')
            ->when($transactionID, function ($query) use ($transactionID) {
                $query->where('transaction.id', $transactionID);
            })
            ->when($businessTypeID, function ($query) use ($businessTypeID) {
                $query->where('transaction.business_type_id', $businessTypeID);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($businessOwnerID) {
                        $query->where('transaction.business_owner_id', $businessOwnerID)
                            ->orWhere(function ($query) use ($businessOwnerID) {
                                $query->where('business_share_contact.contact_id', $businessOwnerID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_SALE_LIST_SHOP);
                            });
                    });
            })
            ->when($customerID, function ($query) use ($customerID) {
                $query->where('transaction.customer_id', $customerID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('transaction.business_id', $businessID);
            })
            ->when(!empty($startDate) && !empty($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(transaction.created_at)'), [$startDate, $endDate]);
            })
            ->when($createdDateByMonthYear, function ($query) use ($createdDateByMonthYear) {
                $query->whereRaw("DATE_FORMAT(transaction.created_at, '%M, %Y') = '" . $createdDateByMonthYear . "'");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('transaction.status', $status);
            })
            ->when($sortShop, function ($query) {
                $query->orderBy('business.business_type_id', 'DESC')
                    ->orderBy('transaction.id', 'DESC');
            })
            ->when($sortRecentOrder, function ($query) {
                $query->orderBy('transaction.id', 'DESC');
            })
            ->when($sortTotalAmount, function ($query) {
                $query->orderBy('transaction.total_amount', 'DESC')
                    ->orderBy('transaction.id', 'DESC');
            })
            ->when($sortQty, function ($query) {
                $query->orderBy(DB::raw('COUNT(product_order_list.id)'), 'DESC')
                    ->orderBy('transaction.id', 'DESC');
            })
            ->select(
                'transaction.id',
                'transaction.code',
                'business_type.id as business_type_id',
                'business_type.name as business_type_name',
                'business.id as business_id',
                'business.name as business_name',
                'business_owner.id as business_owner_id',
                'business_owner.fullname as business_owner_name',
                'customer.id as customer_id',
                'customer.fullname as customer_name',
                'transaction.order_type',
                'transaction.payment_type',
                'shipping_address.id as shipping_address_id',
                'shipping_address.address as shipping_address_detail',
                'shipping_address.latitude',
                'shipping_address.longitude',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'bank_account.id as bank_account_id',
                'transaction.account_number',
                'transaction.account_name',
                'transaction.transaction_date',
                'transaction.image as transaction_image',
                'transaction.fullname',
                'transaction.phone',
                'transaction.remark_booking',
                DB::raw('(SELECT COUNT(*) FROM product_order_list WHERE product_order_list.transaction_id = transaction.id) as order_qty'),
                'transaction.status',
                'transaction.created_at as order_date',
                'transaction.total_amount as transaction_amount',
                'transaction.transaction_fee_amount as app_fee_amount',
                'transaction.sell_amount as sub_amount',
                'business.delivery_fee',
                DB::raw("
                    CASE WHEN transaction.order_type = 6 THEN transaction.sell_amount + business.delivery_fee
                    ELSE transaction.sell_amount
                    END as grand_total
                "),
            )
            ->with([
                'productOrderList' => function ($query) {
                    $query->join('product', 'product.id', 'product_order_list.product_id')
                        ->leftjoin('product_order_modifier_option', 'product_order_list.id', 'product_order_modifier_option.product_order_list_id')
                        ->leftjoin('product as main', 'main.id', 'product.parent_id')
                        ->select(
                            'product_order_list.id as id',
                            'product_order_list.transaction_id',
                            'product_order_list.qty',
                            'product_order_list.price',
                            'product_order_list.total_price',
                            'product.id as product_id',
                            'product.name as product_name',
                            // Use COALESCE to get the main product image if the variant image is null
                            DB::raw('COALESCE(product.image, main.image) as product_image'),
                            'product_order_list.concat_modifier',
                            'product.parent_id',
                            'main.id as main_id',
                            'main.name as main_name',
                            DB::raw('product_order_list.total_price + COALESCE(SUM(product_order_modifier_option.modifier_option_price), 0) as sub_total'),
                        )
                        ->groupBy('product_order_list.id')
                        ->with([
                            'productOrderModifierOption' => function ($query) {
                                $query->orderBy('product_order_modifier_option.id','DESC');
                            }
                        ])
                        ->get();
                }
            ])
            ->groupBy('transaction.id')
            ->orderBy('transaction.id', 'desc');
    }

    //List Donation Charity (Can use everywhere)
    public static function listDonationCharity($filter = [], $sort = '')
    {
        //Filter
        $transactionID = isset($filter['donation_id']) ? $filter['donation_id'] : null;
        $businessID = isset($filter['organization_id']) ? $filter['organization_id'] : null;
        $businessOwnerID = isset($filter['organization_owner_id']) ? $filter['organization_owner_id'] : null;
        $customerID = isset($filter['customer_id']) ? $filter['customer_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;

        //Date Range
        $dateRange = isset($filter['date_range']) ? $filter['date_range'] : null;
        $startDate = empty($dateRange['start_date']) ? null : Carbon::parse($dateRange['start_date'])->format('Y-m-d');
        $endDate = empty($dateRange['end_date']) ? null : Carbon::parse($dateRange['end_date'])->format('Y-m-d');

        //Sort
        $sortNewest = $sort == 'newest' ? 'newest' : null;
        $sortPriceDesc = $sort == 'price_desc' ? 'price_desc' : null;
        $sortPriceAsc = $sort == 'price_asc' ? 'price_asc' : null;


        return self::join('contact', 'contact.id', 'transaction.customer_id')
            ->join('business', 'business.id', 'transaction.business_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->where('transaction.business_type_id', BusinessTypeEnum::getCharityOrganization())
            ->when($transactionID, function ($query) use ($transactionID) {
                $query->where('transaction.id', $transactionID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('transaction.business_id', $businessID);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('transaction.business_owner_id', $businessOwnerID);
            })
            ->when($customerID, function ($query) use ($customerID) {
                $query->where('transaction.customer_id', $customerID);
            })
            ->when(!empty($startDate) && !empty($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(transaction.created_at)'), [$startDate, $endDate]);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('transaction.status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact.fullname', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($sortNewest, function ($query) {
                $query->orderBy('transaction.id', 'DESC');
            })
            ->when($sortPriceAsc, function ($query) {
                $query->orderBy('transaction.total_amount');
            })
            ->when($sortPriceDesc, function ($query) {
                $query->orderBy('transaction.total_amount', 'DESC');
            })
            ->select(
                'transaction.id',
                'transaction.code as transaction_code',
                'contact.id as user_donation_id',
                'contact.fullname as user_donation_name',
                'contact.profile_image as user_donation_profile',
                'contact.cover_image as user_donation_cover',
                'contact.phone as user_donation_phone',
                'contact.email as user_donation_email',
                'business.id as organization_id',
                'business.name as organization_name',
                'business.image as organization_image',
                'business.description as organization_description',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'bank_account.id as bank_account_id',
                'transaction.account_number',
                'transaction.account_name',
                'transaction.image as transaction_image',
                'transaction.transaction_date',
                'transaction.total_amount as donation_amount',
                'transaction.created_at as donation_date',
                'transaction.status',
                'transaction.active',
            )
            ->groupBy('transaction.id')
            ->orderBy('transaction.id', 'desc');
    }

    //List Accommodation (Can Use everywhere)
    public static function listAccommodationRoom($filter = [], $sort = '')
    {
        //Filter
        $transactionID = isset($filter['sale_id']) ? $filter['sale_id'] : null;
        $businessID = isset($filter['accommodation_id']) ? $filter['accommodation_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $customerID = isset($filter['customer_id']) ? $filter['customer_id'] : null;
        $createdDateByMonthYear = isset($filter['created_date_by_month_year']) ? $filter['created_date_by_month_year'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;

        //Date Range
        $dateRange = isset($filter['date_range']) ? $filter['date_range'] : null;
        $startDate = empty($dateRange['start_date']) ? null : Carbon::parse($dateRange['start_date'])->format('Y-m-d');
        $endDate = empty($dateRange['end_date']) ? null : Carbon::parse($dateRange['end_date'])->format('Y-m-d');

        return self::join('contact', 'contact.id', 'transaction.customer_id')
            ->join('business', 'business.id', 'transaction.business_id')
            ->leftjoin('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->where('transaction.business_type_id', BusinessTypeEnum::getAccommodation())
            ->when($transactionID, function ($query) use ($transactionID) {
                $query->where('transaction.id', $transactionID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('transaction.business_id', $businessID);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($businessOwnerID) {
                        $query->where('transaction.business_owner_id', $businessOwnerID)
                            ->whereNull('business.deleted_at')
                            ->orWhere(function ($query) use ($businessOwnerID) {
                                $query->where('business_share_contact.contact_id', $businessOwnerID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_BOOKING_LIST_ACCOMMODATION);
                            });
                    });
            })
            ->when($customerID, function ($query) use ($customerID) {
                $query->where('transaction.customer_id', $customerID);
            })
            ->when($status, function ($query) use ($status) {
                $query->when($status == TransactionStatus::getPending(), function ($query) {
                    $query->where(function($query) {
                        $query->where('transaction.status', TransactionStatus::getPending())
                            ->orWhere('transaction.status', TransactionStatus::getPendingPayment())
                            ->orWhere('transaction.status', TransactionStatus::getAuditingPayment());
                    });
                });
                $query->when($status == TransactionStatus::getApproved(), function ($query) {
                    $query->where('transaction.status', TransactionStatus::getApproved());
                });
                $query->when($status == TransactionStatus::getRejected(), function ($query) {
                    $query->where(function ($query) {
                        $query->where('transaction.status', TransactionStatus::getRejected())
                            ->orWhere('transaction.status', TransactionStatus::getRejectPayment())
                            ->orWhere('transaction.status', TransactionStatus::getCancelled());
                        });
                });
            })
            ->when(!empty($startDate) && !empty($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(transaction.created_at)'), [$startDate, $endDate]);
            })
            ->when($createdDateByMonthYear, function ($query) use ($createdDateByMonthYear) {
                $query->whereRaw("DATE_FORMAT(transaction.created_at, '%M, %Y') = '" . $createdDateByMonthYear . "'");
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact.fullname', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'transaction.id',
                'transaction.business_type_id',
                'transaction.business_owner_id',
                'business.id as business_id',
                'business.name as business_name',
                'business.image as accommodation_image',
                'business_owner.fullname as business_owner_name',
                'contact.id as customer_id',
                'contact.fullname as customer_name',
                'contact.phone as customer_phone',
                'transaction.fullname',
                'transaction.email',
                'transaction.phone',
                'business.image as business_image',
                'transaction.code as transaction_code',
                'transaction.check_in_date',
                'transaction.check_out_date',
                'transaction.qty',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'bank_account.id as bank_account_id',
                'transaction.account_number',
                'transaction.account_name',
                'transaction.transaction_date',
                'transaction.total_amount as transaction_amount',
                'transaction.sell_amount',
                DB::raw('COALESCE(transaction_fee_amount , 0) as app_fee_amount'),
                'transaction.image as transaction_image',
                'transaction.remark',
                'transaction.remark_booking',
                'transaction.status',
                'transaction.created_at as booking_date'
            )
            ->with([
                'bookList' => function ($query) {
                    $query->select(
                        'book_list.transaction_id',
                        'room.id',
                        'room_type.id as room_type_id',
                        'room_type.name as room_type_name',
                        'room.name',
                        'room.image',
                        'room.total_price',
                    )
                    ->leftjoin('room_type', 'room_type.id', 'room.room_type_id')
                    ->groupBy('book_list.id')
                    ->get();
                }
            ])
            ->groupBy('transaction.id')
            ->orderBy('transaction.id', 'desc');
    }

    //List Massage (Can Use everywhere)
    public static function listMassage($filter = '', $sort = '')
    {
        //Filter
        $transactionID = isset($filter['sale_id']) ? $filter['sale_id'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $customerID = isset($filter['customer_id']) ? $filter['customer_id'] : null;
        $massagerID = isset($filter['massager_id']) ? $filter['massager_id'] : null;
        $createdDateByMonthYear = isset($filter['created_date_by_month_year']) ? $filter['created_date_by_month_year'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;


        return self::join('contact', 'contact.id', 'transaction.customer_id')
            ->join('business', 'business.id', 'transaction.business_id')
            ->leftjoin('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->leftjoin('shipping_address', 'shipping_address.id', 'transaction.shipping_address_id')
            ->leftJoin('transaction_contact', function ($join) {
                $join->on('transaction.id', '=', 'transaction_contact.transaction_id');
            })
            ->where('transaction.business_type_id', BusinessTypeEnum::getMassage())
            ->when($transactionID, function ($query) use ($transactionID) {
                $query->where('transaction.id', $transactionID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('transaction.business_id', $businessID);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($businessOwnerID) {
                        $query->where('transaction.business_owner_id', $businessOwnerID)
                            ->whereNull('business.deleted_at')
                            ->orWhere(function ($query) use ($businessOwnerID) {
                                $query->where('business_share_contact.contact_id', $businessOwnerID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_SALE_LIST_MASSAGE);
                            });
                    });
            })
            ->when($customerID, function ($query) use ($customerID) {
                $query->where('transaction.customer_id', $customerID)
                    ->where('transaction.active', TransactionActive::getEnable());
            })
            ->when($massagerID, function ($query) use ($massagerID) {
                $query->where('transaction_contact.contact_id', $massagerID)
                    ->where('transaction.status', TransactionStatus::getApproved());
            })
            ->when($status, function ($query) use ($status) {
                $query->when($status == TransactionStatus::getPending(), function ($query) {
                    $query->where(function ($query) {
                        $query->where('transaction.status', TransactionStatus::getPending())
                            ->orWhere('transaction.status', TransactionStatus::getPendingPayment())
                            ->orWhere('transaction.status', TransactionStatus::getAuditingPayment());
                    });
                });
                $query->when($status == TransactionStatus::getApproved(), function ($query) {
                    $query->where('transaction.status', TransactionStatus::getApproved());
                });
                $query->when($status == TransactionStatus::getRejected(), function ($query) {
                    $query->where(function ($query) {
                        $query->where('transaction.status', TransactionStatus::getRejected())
                            ->orWhere('transaction.status', TransactionStatus::getRejectPayment())
                            ->orWhere('transaction.status', TransactionStatus::getCancelled());
                    });
                });
            })
            ->when($createdDateByMonthYear, function ($query) use ($createdDateByMonthYear) {
                $query->whereRaw("DATE_FORMAT(transaction.created_at, '%M, %Y') = '" . $createdDateByMonthYear . "'");
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact.fullname', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'transaction.id',
                'transaction.business_type_id',
                'business.id as business_id',
                'business.name as business_name',
                'transaction.business_owner_id',
                'business_owner.fullname as business_owner_name',
                'contact.id as customer_id',
                'contact.fullname as customer_name',
                'contact.phone as customer_phone',
                'transaction.fullname',
                'transaction.email',
                'transaction.phone',
                'business.image as business_image',
                'transaction.order_type',
                'shipping_address.id as shipping_address_id',
                'shipping_address.address as shipping_address_detail',
                'shipping_address.latitude',
                'shipping_address.longitude',
                'transaction.code as transaction_code',
                'transaction.check_in_date',
                'transaction.qty',
                'transaction.total_tip_amount',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'bank_account.id as bank_account_id',
                'transaction.account_number',
                'transaction.account_name',
                'transaction.transaction_date',
                'transaction.total_amount as transaction_amount',
                'transaction.sell_amount',
                DB::raw('SUM(DISTINCT transaction.sell_amount + total_tip_amount) as grand_total'),
                DB::raw('COALESCE(transaction_fee_amount , 0) as app_fee_amount'),
                'transaction.image as transaction_image',
                'transaction.remark',
                'transaction.remark_booking',
                'transaction.status',
                'transaction.active',
                'transaction.created_at as booking_date',
                DB::raw('COUNT(transaction_contact.transaction_id) as total_massager')
            )
            ->with([
                'transactionContactList' => function ($query) {
                $query->join('business_staff', 'business_staff.contact_id', 'transaction_contact.contact_id')
                        ->join('contact', 'contact.id', 'business_staff.contact_id')
                        ->leftjoin('business', 'transaction_contact.business_id', 'business.id')
                        ->select(
                            'transaction_contact.id',
                            'transaction_contact.transaction_id',
                            'transaction_contact.business_id',
                            'transaction_contact.contact_id',
                            'contact.profile_image',
                            'contact.gender',
                            'business_staff.code',
                            'transaction_contact.tip_amount',
                        )
                        ->groupBy('transaction_contact.id')
                        ->get();
                },
                'transactionContactDetailList' => function ($query) {
                    $query->select(
                        'transaction_contact_detail.id',
                        'transaction_contact_detail.transaction_id',
                        'transaction_contact_detail.business_id',
                        'transaction_contact_detail.contact_id',
                        'transaction_contact_detail.start_time',
                        'transaction_contact_detail.end_time',
                        DB::raw("CONCAT(transaction_contact_detail.start_time,' - ',transaction_contact_detail.end_time) as time")
                    )
                    ->get();
                },
                'productOrderList' => function ($query) {
                    $query->join('product', 'product.id', 'product_order_list.product_id')
                        ->select(
                            'product_order_list.id as id',
                            'product_order_list.transaction_id',
                            'product_order_list.qty',
                            'product_order_list.price',
                            'product_order_list.total_price',
                            'product.id as product_id',
                            'product.name as product_name',
                            'product.image as product_image',
                            'product.duration',
                        )
                        ->get();
                }
            ])
            ->groupBy('transaction.id')
            ->orderBy('transaction.id', 'desc');
    }

    //List Attraction Can Use (Everywhere)
    public static function listAttraction($filter = [], $sortBy = "")
    {
        //Filter
        $transactionID = isset($filter['sale_id']) ? $filter['sale_id'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $customerID = isset($filter['customer_id']) ? $filter['customer_id'] : null;
        $createdDateByMonthYear = isset($filter['created_date_by_month_year']) ? $filter['created_date_by_month_year'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;

        return self::join('contact', 'contact.id', 'transaction.customer_id')
            ->join('business', 'business.id', 'transaction.business_id')
            ->leftjoin('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->where('transaction.business_type_id', BusinessTypeEnum::getAttraction())
            ->when($transactionID, function ($query) use ($transactionID) {
                $query->where('transaction.id', $transactionID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('transaction.business_id', $businessID);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($businessOwnerID) {
                        $query->where('transaction.business_owner_id', $businessOwnerID)
                            ->whereNull('business.deleted_at')
                            ->orWhere(function ($query) use ($businessOwnerID) {
                                $query->where('business_share_contact.contact_id', $businessOwnerID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_SALE_LIST_ATTRACTION);
                            });
                    });
            })
            ->when($customerID, function ($query) use ($customerID) {
                $query->where('transaction.customer_id', $customerID);
            })
            ->when($status, function ($query) use ($status) {
                $query->when($status == TransactionStatus::getPending(), function ($query) {
                    $query->where(function ($query) {
                        $query->where('transaction.status', TransactionStatus::getPending());
                    });
                });
                $query->when($status == TransactionStatus::getApproved(), function ($query) {
                    $query->where('transaction.status', TransactionStatus::getApproved());
                });
                $query->when($status == TransactionStatus::getRejected(), function ($query) {
                    $query->where(function ($query) {
                        $query->where('transaction.status', TransactionStatus::getRejected())
                            ->orWhere('transaction.status', TransactionStatus::getCancelled());
                    });
                });
            })
            ->when($createdDateByMonthYear, function ($query) use ($createdDateByMonthYear) {
                $query->whereRaw("DATE_FORMAT(transaction.created_at, '%M, %Y') = '" . $createdDateByMonthYear . "'");
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact.fullname', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'transaction.id',
                'transaction.business_type_id',
                'business.id as business_id',
                'business.name as business_name',
                'business.image as business_image',
                'transaction.business_owner_id',
                'business_owner.fullname as business_owner_name',
                'contact.id as customer_id',
                'contact.fullname as customer_name',
                'contact.phone as customer_phone',
                'transaction.fullname',
                'transaction.email',
                'transaction.phone',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'bank_account.id as bank_account_id',
                'transaction.account_number',
                'transaction.account_name',
                'transaction.transaction_date',
                'transaction.total_amount as transaction_amount',
                'transaction.sell_amount',
                DB::raw('COALESCE(transaction_fee_amount , 0) as app_fee_amount'),
                'transaction.image as transaction_image',
                'transaction.remark',
                'transaction.remark_booking',
                'transaction.check_in_date',
                'transaction.status',
                'transaction.created_at as booking_date',
            )
            ->with([
                'productOrderList' => function ($query) {
                    $query->join('place_price_list', 'place_price_list.id', 'product_order_list.product_id')
                    ->join('category', 'category.id', 'place_price_list.category_id')
                    ->select(
                        'product_order_list.id',
                        'product_order_list.transaction_id',
                        'place_price_list.category_id',
                        'category.name as category_name',
                        'product_order_list.product_id',
                        'place_price_list.name as product_name',
                        'place_price_list.image as product_image',
                        'product_order_list.qty',
                        'product_order_list.price',
                        'product_order_list.total_price',
                    )
                    ->get();
                }
            ])
            ->groupBy('transaction.id')
            ->orderBy('transaction.id', 'desc');
    }

    // List KTV Can Use (Everywhere)
    public static function listKTV($filter = [], $sortBy = "")
    {
        //Filter
        $transactionID = isset($filter['sale_id']) ? $filter['sale_id'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $customerID = isset($filter['customer_id']) ? $filter['customer_id'] : null;
        $ktvGirlID = isset($filter['ktv_girl_id']) ? $filter['ktv_girl_id'] : null;
        $createdDateByMonthYear = isset($filter['created_date_by_month_year']) ? $filter['created_date_by_month_year'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;

        return self::join('contact', 'contact.id', 'transaction.customer_id')
        ->join('business', 'business.id', 'transaction.business_id')
        ->leftjoin('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
        ->leftjoin('bank_account', 'bank_account.id', 'transaction.bank_account_id')
        ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
        ->leftJoin('transaction_contact', function ($join) {
            $join->on('transaction.id', '=', 'transaction_contact.transaction_id');
        })
        ->where('transaction.business_type_id', BusinessTypeEnum::getKtv())
        ->when($transactionID, function ($query) use ($transactionID) {
            $query->where('transaction.id', $transactionID);
        })
        ->when($businessID, function ($query) use ($businessID) {
            $query->where('transaction.business_id', $businessID);
        })
        ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
            /**
             * Example Raw SQL At Where Area
             * https://prnt.sc/z1rmrddA944f
             */
            $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'business.id')
                ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                ->where(function ($query) use ($businessOwnerID) {
                    $query->where('transaction.business_owner_id', $businessOwnerID)
                        ->whereNull('business.deleted_at')
                        ->orWhere(function ($query) use ($businessOwnerID) {
                            $query->where('business_share_contact.contact_id', $businessOwnerID)
                                ->where('business_permission.action', BusinessPermission::VIEW_SALE_LIST_KTV);
                        });
                });
        })
        ->when($customerID, function ($query) use ($customerID) {
            $query->where('transaction.customer_id', $customerID)
                ->where('transaction.active', '!=', TransactionActive::getDisable());
        })
        ->when($ktvGirlID, function ($query) use ($ktvGirlID) {
            $query->where('transaction_contact.contact_id', $ktvGirlID)
                ->where('transaction.status', TransactionStatus::getApproved());
        })
        ->when($status, function ($query) use ($status) {
            $query->when($status == TransactionStatus::getPending(), function ($query) {
                $query->where(function ($query) {
                    $query->where('transaction.status', TransactionStatus::getPending())
                        ->orWhere('transaction.status', TransactionStatus::getPendingPayment())
                        ->orWhere('transaction.status', TransactionStatus::getAuditingPayment());
                });
            });
            $query->when($status == TransactionStatus::getApproved(), function ($query) {
                $query->where(function ($query) {
                    $query->where('transaction.status', TransactionStatus::getApproved())
                        ->orWhere('transaction.status', TransactionStatus::getCompleted());
                });
            });
            $query->when($status == TransactionStatus::getRejected(), function ($query) {
                $query->where(function ($query) {
                    $query->where('transaction.status', TransactionStatus::getRejected())
                        ->orWhere('transaction.status', TransactionStatus::getRejectPayment())
                        ->orWhere('transaction.status', TransactionStatus::getCancelled());
                });
            });
        })
        ->when($createdDateByMonthYear, function ($query) use ($createdDateByMonthYear) {
            $query->whereRaw("DATE_FORMAT(transaction.created_at, '%M, %Y') = '" . $createdDateByMonthYear . "'");
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('business.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact.fullname', 'LIKE', '%' . $search . '%');
            });
        })
        ->select(
            'transaction.id',
            'transaction.business_type_id',
            'business.id as business_id',
            'business.name as business_name',
            'transaction.business_owner_id',
            'business_owner.fullname as business_owner_name',
            'contact.id as customer_id',
            'contact.fullname as customer_name',
            'contact.phone as customer_phone',
            'transaction.fullname',
            'transaction.email',
            'transaction.phone',
            'business.image as business_image',
            'transaction.code as transaction_code',
            DB::raw('DATE(transaction.check_in_date) as booking_date'),
            DB::raw('TIME(transaction.check_in_date) as start_time'),
            DB::raw('TIME(transaction.check_out_date) as close_time'),
            'bank.id as bank_id',
            'bank.name as bank_name',
            'bank.image as bank_image',
            'bank_account.id as bank_account_id',
            'transaction.transaction_date',
            'transaction.total_amount as transaction_amount',
            'transaction.sell_amount',
            DB::raw('COALESCE(transaction_fee_amount , 0) as app_fee_amount'),
            'transaction.image as transaction_image',
            'transaction.remark',
            'transaction.remark_booking',
            'transaction.status',
            'transaction.active',
            'transaction.created_at',
        )
        ->with([
            'bookList' => function ($query) {
                $query->select(
                    'book_list.transaction_id',
                    'room.id',
                    'room.name',
                    'room.code',
                    'room.image',
                    'room.total_price',
                )
                ->groupBy('book_list.id')
                ->get();
            },
            'transactionContactList' => function ($query) {
                $query->join('business_staff', 'business_staff.contact_id', 'transaction_contact.contact_id')
                ->join('contact', 'contact.id', 'business_staff.contact_id')
                ->join('contact_business_info', function ($join) {
                    $join->on('contact_business_info.contact_id', '=', 'contact.id')
                    ->where('contact_business_info.business_type_id', '=', BusinessTypeEnum::getKtv());
                })
                ->leftjoin('business', 'transaction_contact.business_id', 'business.id')
                ->select(
                    'transaction_contact.id',
                    'transaction_contact.transaction_id',
                    'transaction_contact.business_id',
                    'transaction_contact.contact_id',
                    'business_staff.code',
                    'contact_business_info.image',
                    'transaction_contact.price',
                )
                ->groupBy('transaction_contact.id')
                ->get();
            },
            'productOrderList' => function ($query) {
                $query->join('product', 'product.id', 'product_order_list.product_id')
                ->select(
                    'product_order_list.id as id',
                    'product_order_list.transaction_id',
                    'product_order_list.qty',
                    'product_order_list.price',
                    'product_order_list.total_price',
                    'product.id as product_id',
                    'product.name as product_name',
                    'product.image as product_image',
                )
                ->get();
            }
        ])
        ->groupBy('transaction.id')
        ->orderBy('transaction.id', 'desc');
    }
}

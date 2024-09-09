<?php

namespace App\Models;

use App\Enums\Types\AgencyType;
use App\Enums\Types\ContactHasPermission;
use App\Enums\Types\IsBusinessOwner;
use App\Enums\Types\PropertyCommissionWithdrawingStatus;
use App\Enums\Types\PropertyTypeEnum;
use App\Enums\Types\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertyCommission extends Model
{
    const TABLE_NAME = 'property_commission';
    const ID = 'id';
    const TRANSACTION_ID = 'transaction_id';
    const PROPERTY_ID = 'property_id';
    const AGENCY_ID = 'agency_id';
    const AGENCY_TYPE = 'agency_type';
    const PROPERTY_ASSET_ID = 'property_asset_id';
    const COMMISSION = 'commission';
    const COMMISSION_TYPE = 'commission_type';
    const COMMISSION_AMOUNT = 'commission_amount';
    const WITHDRAWN_AMOUNT = 'withdrawn_amount';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::TRANSACTION_ID} = $data[self::TRANSACTION_ID];
        $this->{self::PROPERTY_ID} = $data[self::PROPERTY_ID];
        $this->{self::AGENCY_ID} = $data[self::AGENCY_ID];
        $this->{self::AGENCY_TYPE} = $data[self::AGENCY_TYPE];
        isset($data[self::PROPERTY_ASSET_ID]) && $this->{self::PROPERTY_ASSET_ID} = $data[self::PROPERTY_ASSET_ID];
        $this->{self::COMMISSION} = $data[self::COMMISSION];
        $this->{self::COMMISSION_TYPE} = $data[self::COMMISSION_TYPE];
        $this->{self::COMMISSION_AMOUNT} = $data[self::COMMISSION_AMOUNT];
        $this->{self::WITHDRAWN_AMOUNT} = $data[self::WITHDRAWN_AMOUNT];

    }

    //List Property
    public static function listProperty($filter = [])
    {
        //Filter
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $agencyID = isset($filter['agency_id']) ? $filter['agency_id'] : null;
        $saleAssistanceID = isset($filter['sale_assistance_id']) ? $filter['sale_assistance_id'] : null;
        $propertyType = isset($filter['property_type']) ? $filter['property_type'] : null;
        $agencyType = isset($filter['agency_type']) ? $filter['agency_type'] : null;

        return self::select(
            'property_commission.id',
            'property_commission.property_id',
            'business.name as property_name',
            'business_owner.id as business_owner_id',
            'business_owner.fullname as business_owner_name',
            'business_owner.profile_image as business_owner_profile',
            'business.image as thumbnail',
            'contact.id as agency_id',
            'contact.fullname as agency_name',
            'property_commission.agency_type',
            DB::raw('CASE
                WHEN property_type.type = "' . PropertyTypeEnum::getMulti() . '"
                    THEN COUNT(property_commission.property_asset_id)
                ELSE
                    0
                END total_asset
                '),
            DB::raw('CASE
                WHEN property_type.type = "' . PropertyTypeEnum::getMulti() . '"
                    THEN SUM(property_commission.commission_amount)
                ELSE
                    property_commission.commission_amount
                END commission_amount
                '),
            DB::raw('CASE
                WHEN property_type.type = "' . PropertyTypeEnum::getMulti() . '"
                    THEN SUM(property_commission.withdrawn_amount)
                ELSE
                    property_commission.withdrawn_amount
                END withdrawn_amount
                '),
            DB::raw('CASE
                WHEN property_type.type = "' . PropertyTypeEnum::getMulti() . '"
                    THEN sum(property_commission.commission_amount) - sum(property_commission.withdrawn_amount)
                ELSE
                    property_commission.commission_amount - property_commission.withdrawn_amount
                END remain_amount
                '),
            'transaction.remark',
            'transaction.status as transaction_status',
            DB::raw("COUNT(property_commission_withdrawing.id) as pending_withdrawing")
        )
            ->join('business', 'business.id', 'property_commission.property_id')
            ->join('property_type', 'property_type.id', 'business.property_type_id')
            ->join('transaction', 'transaction.id', 'property_commission.transaction_id')
            ->join('contact', 'contact.id', 'property_commission.agency_id')
            ->leftjoin('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
            ->leftjoin('property_commission_withdrawing', function ($join) {
                $join->on('property_commission_withdrawing.property_commission_id', 'property_commission.id')
                    ->where('property_commission_withdrawing.status', PropertyCommissionWithdrawingStatus::getPending());
            })
            ->where(function ($query) {
                $query->where('transaction.status', TransactionStatus::getCompleted())
                    ->orWhere('transaction.status', TransactionStatus::getCancelled());
            })
            //For Business Owner
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
                                    ->where('business_permission.action', BusinessPermission::VIEW_COMMISSION_LIST_PROPERTY);
                            });
                    });
            })
            //For Agency
            ->when($agencyID, function ($query) use ($agencyID) {
                $query->where('property_commission.agency_id', $agencyID)
                    ->where(function ($query) {
                        $query->where('property_commission.agency_type', AgencyType::getBase())
                            ->OrWhere('property_commission.agency_type', AgencyType::getReferral());
                    });
            })
            //For Sale Assistant
            ->when($saleAssistanceID, function ($query) use ($saleAssistanceID) {
                $query->where('property_commission.agency_id', $saleAssistanceID)
                    ->where('property_commission.agency_type', AgencyType::getSaleAssistance());
            })
            //For Business Owner
            ->when($agencyType, function ($query) use ($agencyType) {
                $query->where('property_commission.agency_type', $agencyType);
            })
            // For All
            ->when($propertyType, function ($query) use ($propertyType) {
                if ($propertyType == PropertyTypeEnum::getMulti()) {
                    $query->where('property_type.type', PropertyTypeEnum::getMulti())
                        ->groupBy('property_commission.property_id');
                } else if ($propertyType == PropertyTypeEnum::getSingle()) {
                    $query->where('property_type.type', PropertyTypeEnum::getSingle())
                        ->groupBy('property_commission.id');
                }
            });
    }

    //List Asset
    public static function listAsset($filter = [])
    {
        //Filter
        $propertyID = isset($filter['property_id']) ? $filter['property_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : false;
        $agencyID = isset($filter['agency_id']) ? $filter['agency_id'] : null;
        $saleAssistanceID = isset($filter['sale_assistance_id']) ? $filter['sale_assistance_id'] : null;
        $agencyType = isset($filter['agency_type']) ? $filter['agency_type'] : null;

        return self::join('property_asset', 'property_asset.id', 'property_commission.property_asset_id')
            ->join('asset_category', 'asset_category.id', 'property_asset.asset_category_id')
            ->join('contact', 'contact.id', 'property_commission.agency_id')
            ->join('transaction', 'transaction.id', 'property_commission.transaction_id')
            ->leftjoin('contact as business_owner', 'business_owner.id', 'transaction.business_owner_id')
            ->where(function ($query) {
                $query->where('transaction.status', TransactionStatus::getCompleted())
                    ->orWhere('transaction.status', TransactionStatus::getCancelled());
            })
            ->when($propertyID, function ($query) use ($propertyID) {
                $query->where('property_commission.property_id', $propertyID);
            })
            //For Business Owner
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                /**
                 * Example Raw SQL At Where Area
                 * https://prnt.sc/z1rmrddA944f
                 */
                $query->leftjoin('business_share_contact', 'business_share_contact.business_id', 'property_commission.property_id')
                    ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
                    ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
                    ->where(function ($query) use ($businessOwnerID) {
                        $query->where('transaction.business_owner_id', $businessOwnerID)
                            ->orWhere(function ($query) use ($businessOwnerID) {
                                $query->where('business_share_contact.contact_id', $businessOwnerID)
                                    ->where('business_permission.action', BusinessPermission::VIEW_COMMISSION_LIST_PROPERTY);
                            });
                    });
            })
            //For Agency
            ->when($agencyID, function ($query) use ($agencyID) {
                $query->where('property_commission.agency_id', $agencyID)
                    ->where(function ($query) {
                        $query->where('property_commission.agency_type', AgencyType::getBase())
                            ->OrWhere('property_commission.agency_type', AgencyType::getReferral());
                    });
            })
            //For Sale Assistant
            ->when($saleAssistanceID, function ($query) use ($saleAssistanceID) {
                $query->where('property_commission.agency_id', $saleAssistanceID)
                    ->where('property_commission.agency_type', AgencyType::getSaleAssistance());
            })
            //For Business Owner
            ->when($agencyType, function ($query) use ($agencyType) {
                $query->where('property_commission.agency_type', $agencyType);
            })
            ->select(
                'property_commission.id',
                'property_commission.property_id',
                'property_commission.property_asset_id',
                'business_owner.id as business_owner_id',
                'business_owner.fullname as business_owner_name',
                'business_owner.profile_image as business_owner_profile',
                'property_asset.code as asset_code',
                'property_asset.size as asset_size',
                'asset_category.id as asset_category_id',
                'asset_category.name as asset_category_name',
                'contact.id as agency_id',
                'contact.fullname as agency_name',
                'property_commission.agency_type',
                'property_asset.image as thumbnail',
                'property_commission.commission_amount',
                'property_commission.withdrawn_amount',
                DB::raw("(property_commission.commission_amount - property_commission.withdrawn_amount) remain_amount "),
                'transaction.remark',
                'transaction.status as transaction_status',
                DB::raw("
                (
                    SELECT COUNT(pcw.id)
                    FROM property_commission_withdrawing pcw
                    JOIN property_commission pc ON pc.id = pcw.property_commission_id
                    JOIN transaction tst ON tst.id = pc.transaction_id
                    LEFT JOIN business_share_contact bsc ON bsc.business_id = pc.property_id
                    LEFT JOIN business_contact_permission bpc ON bpc.business_share_contact_id = bsc.id
                    LEFT JOIN business_permission bp ON bp.id = bpc.business_permission_id
                    WHERE pcw.property_commission_id = property_commission.id AND pcw.status = '" . PropertyCommissionWithdrawingStatus::getPending() . "' AND
                    CASE
                    WHEN '" . $businessOwnerID . "' != '' THEN (tst.business_owner_id = '" . $businessOwnerID . "' OR (bsc.contact_id = '" . $businessOwnerID . "' AND bp.action = '" . BusinessPermission::VIEW_COMMISSION_LIST_PROPERTY . "'))
                    WHEN '" . $saleAssistanceID . "' != '' THEN pc.agency_id = '" . $saleAssistanceID . "' AND pc.agency_type = '" . AgencyType::getSaleAssistance() . "'
                    WHEN '" . $agencyID . "' != '' THEN pc.agency_id = '" . $agencyID . "' AND (pc.agency_type = '" . AgencyType::getBase() . "' OR pc.agency_type = '" . AgencyType::getReferral() . "')
                    WHEN '" . $agencyType . "' != '' THEN property_commission.agency_type = '" . $agencyType . "'
                    END
                    GROUP BY pc.id
                )
                as pending_withdrawing"
                )
            )
            ->groupBy('property_commission.id');
    }


    //Withdrawing History By Transaction (Use only Admin)
    public static function listWithdrawingHistoryByTransaction($filter = [])
    {
        $transactionID = isset($filter['transaction_id']) ? $filter['transaction_id'] : null;

        return self::join('property_commission_withdrawing', 'property_commission_withdrawing.property_commission_id', 'property_commission.id')
            ->when($transactionID, function ($query) use ($transactionID) {
                $query->where('property_commission.transaction_id', $transactionID);
            })
            ->select(
                'property_commission.id',
                'property_commission.commission_amount',
                'property_commission.withdrawn_amount',
                DB::raw("(property_commission.commission_amount - property_commission.withdrawn_amount) as remain_amount"),
                'property_commission_withdrawing.id as property_commission_withdrawing_id',
                'property_commission_withdrawing.withdraw_date',
                'property_commission_withdrawing.withdraw_amount',
                'property_commission_withdrawing.transaction_date',
                'property_commission_withdrawing.transaction_image',
            )
            ->groupBy('property_commission_withdrawing.id');
    }
}

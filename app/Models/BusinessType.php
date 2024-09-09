<?php

namespace App\Models;

use App\Enums\Types\BusinessTypeHasTransaction;
use App\Enums\Types\BusinessTypeStatus;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{

    const TABLE_NAME = 'business_type';
    const ID = 'id';
    const APP_TYPE_ID = 'app_type_id';
    const NAME = 'name';
    const HAS_TRANSACTION = 'has_transaction';
    const IMAGE = 'image';
    const ORDER = 'order';
    const APP_FEE = 'app_fee';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //List
    public static function lists($filter = [], $sortBy = "", $sortType = "desc")
    {
        //Filter Admin
        $hasTransaction = isset($filter['has_transaction']) ? $filter['has_transaction'] : null;
        $hasTransaction = $hasTransaction == "0" ? 2 : $hasTransaction;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;

        //Sort Admin
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortOrder = $sortBy == 'order' ? 'order' : null;

        return self::select(
            'id',
            'name',
            'image',
            'has_transaction',
            'order',
            'app_fee',
            'status',
        )
        ->when($hasTransaction, function ($query) use ($hasTransaction) {
            if($hasTransaction == 2) {
                $query->where('business_type.has_transaction', BusinessTypeHasTransaction::getNo());
            } else {
                $query->where('business_type.has_transaction', $hasTransaction);
            }
        })
        ->when($status, function ($query) use ($status) {
            if ($status == 2) {
                $query->where('business_type.status', BusinessTypeStatus::getDisable());
            } else {
                $query->where('business_type.status', $status);
            }
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('business_type.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($sortName, function ($query) use ($sortType) {
            $query->orderBy("business_type.name", $sortType);
        })
        ->when($sortOrder, function ($query) use ($sortType) {
            $query->orderBy("business_type.order", $sortType);
        })
        ->orderBy('business_type.order', 'asc');
    }

    //Set Data
    public function setData($data)
    {
        isset($data[self::APP_TYPE_ID]) && $this->{self::APP_TYPE_ID} = $data[self::APP_TYPE_ID];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::HAS_TRANSACTION} = $data[self::HAS_TRANSACTION];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        isset($data[self::APP_FEE]) && $this->{self::APP_FEE} = $data[self::APP_FEE];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    //Get App Fee
    public static function getTransactionFee($business_type)
    {
        $fee = 0;
        $data = BusinessType::select(self::APP_FEE)->where(self::ID, $business_type)->first();
        if(!empty($data)) {
            $fee = $data->{self::APP_FEE};
        }
        return $fee;
    }
}

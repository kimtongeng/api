<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessCategoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class BusinessCategory extends Model
{
    use SoftDeletes;
    const TABLE_NAME = 'business_category';
    const ID    = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const NAME  = 'name';
    const IMAGE = 'image';
    const ORDER = 'order';
    const TYPE = 'type';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    public static function lists($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $NameByKey = isset($filter['name_by_key']) ? $filter['name_by_key'] : null;
        $business_type = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortStatus = $sortBy == 'status' ? 'status' : null;
        $sortOrderNumber = $sortBy == 'order' ? 'order' : null;


        return self::when($business_type, function ($query) use ($business_type) {
            $query->where('business_category.business_type_id', $business_type);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('business_category.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($status, function ($query) use ($status) {
            if ($status == 2) {
                $query->where('business_category.status', BusinessCategoryStatus::getDisabled());
            } else {
                $query->where('business_category.status', $status);
            }
        })
        ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
            $query->whereBetween('business_category.created_at', [$createdAtStartDate, $createdAtEndDate]);
        })
        ->when($sortName, function ($query) use ($sortType) {
            $query->orderBy('business_category.name', $sortType);
        })
        ->when($sortOrderNumber, function ($query) use ($sortType) {
            $query->orderBy('business_category.order', $sortType);
        })
        ->when($sortCreatedAt, function ($query) use ($sortType) {
            $query->orderBy('business_category.created_at', $sortType);
        })
        ->when($sortStatus, function ($query) use ($sortType) {
            $query->orderBy('business_category.status', $sortType);
        })
        ->select(
            'business_category.id',
            'business_category.image',
            'business_category.business_type_id',
            DB::raw("
                CASE
                    WHEN JSON_VALID(business_category.name) THEN
                        JSON_UNQUOTE(JSON_EXTRACT(business_category.name, '$.{$NameByKey}'))
                    ELSE
                        business_category.name
                END AS `name`
            "),
            'business_category.order',
            'business_category.created_at',
            'business_category.status'
        );
    }

    public static function listsAdmin($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $business_type = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortStatus = $sortBy == 'status' ? 'status' : null;
        $sortOrderNumber = $sortBy == 'order' ? 'order' : null;

        return self::when($business_type, function ($query) use ($business_type) {
                $query->where('business_category.business_type_id', $business_type);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('business_category.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 2) {
                    $query->where('business_category.status', BusinessCategoryStatus::getDisabled());
                } else {
                    $query->where('business_category.status', $status);
                }
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('business_category.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortName, function ($query) use ($sortType) {
                $query->orderBy('business_category.name', $sortType);
            })
            ->when($sortOrderNumber, function ($query) use ($sortType) {
                $query->orderBy('business_category.order', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('business_category.created_at', $sortType);
            })
            ->when($sortStatus, function ($query) use ($sortType) {
                $query->orderBy('business_category.status', $sortType);
            })
            ->select(
                'business_category.id',
                'business_category.image',
                'business_category.business_type_id',
                'business_category.name',
                'business_category.order',
                'business_category.created_at',
                'business_category.status',
            );
    }

    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::NAME} = $data[self::NAME];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        isset($data[self::TYPE]) && $this->{self::TYPE} = $data[self::TYPE];
        $this->{self::STATUS} = $data[self::STATUS];
    }
}

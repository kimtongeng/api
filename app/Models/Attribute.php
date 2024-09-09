<?php

namespace App\Models;

use App\Enums\Types\AttributeStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'attribute';
    const ATTRIBUTE_GROUP_ID = 'attribute_group_id';
    const ID    = 'id';
    const IMAGE = 'image';
    const NAME = 'name';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::ATTRIBUTE_GROUP_ID} = $data[self::ATTRIBUTE_GROUP_ID];
        $this->{self::IMAGE} = $data[self::IMAGE];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    public static function lists($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $attribute_group = isset($filter['attribute_group_id']) ? $filter['attribute_group_id'] : null;
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

        return self::select(
            'attribute.id',
            'attribute_group.id as attribute_group_id',
            'attribute_group.name as attribute_group_name',
            'attribute_group.key as attribute_group_key',
            'attribute.name',
            'attribute.image',
            'attribute.created_at',
            'attribute.status',
        )
        ->join('attribute_group','attribute_group.id','attribute.attribute_group_id')
        ->when($attribute_group, function ($query) use ($attribute_group) {
            $query->where('attribute.attribute_group_id', $attribute_group);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('attribute.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($status, function ($query) use ($status) {
            if ($status == 2) {
                $query->where('attribute.status', AttributeStatus::getDisabled());
            } else {
                $query->where('attribute.status', $status);
            }
        })
        ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
            $query->whereBetween('attribute.created_at', [$createdAtStartDate, $createdAtEndDate]);
        })
        ->when($sortName, function ($query) use ($sortType) {
            $query->orderBy('attribute.name', $sortType);
        })
        ->when($sortCreatedAt, function ($query) use ($sortType) {
            $query->orderBy('attribute.created_at', $sortType);
        })
        ->when($sortStatus, function ($query) use ($sortType) {
            $query->orderBy('attribute.status', $sortType);
        });
    }
}

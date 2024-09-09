<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    const TABLE_NAME = 'item_type';
    const ID = 'id';
    const NAME = 'name';
    const IMAGE = 'image';
    const ORDER = 'order';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
    }

    public static function lists($filter = [], $sortBy = "", $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortOrderNumber = $sortBy == 'order' ? 'order' : null;

        return self::when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('item_type.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('item_type.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortName, function ($query) use ($sortType) {
                $query->orderBy('item_type.name', $sortType);
            })
            ->when($sortOrderNumber, function ($query) use ($sortType) {
                $query->orderBy('item_type.order', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('item_type.created_at', $sortType);
            })
            ->select(
                'item_type.id',
                'item_type.name',
                'item_type.image',
                'item_type.order',
                'item_type.created_at',
            )
            ->orderBy('item_type.order', 'asc');
    }
}

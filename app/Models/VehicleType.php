<?php

namespace App\Models;

use App\Enums\Types\VehicleTypeStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleType extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'vehicle_type';
    const ID = 'id';
    const NAME = 'name';
    const IMAGE = 'image';
    const ORDER = 'order';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    /*
     * Relationship Area
     *
    */
    //Vehicle Distance Price
    public function vehiclePrice()
    {
        return $this->hasMany(VehicleDistancePrice::class, VehicleDistancePrice::VEHICLE_ID, self::ID);
    }

    //Set Data Area
    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    public static function lists($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;
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

        return self::when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('vehicle_type.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 2) {
                    $query->where('vehicle_type.status', VehicleTypeStatus::getDisabled());
                } else {
                    $query->where('vehicle_type.status', $status);
                }
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('vehicle_type.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortName, function ($query) use ($sortType) {
                $query->orderBy('vehicle_type.name', $sortType);
            })
            ->when($sortOrderNumber, function ($query) use ($sortType) {
                $query->orderBy('vehicle_type.order', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('vehicle_type.created_at', $sortType);
            })
            ->when($sortStatus, function ($query) use ($sortType) {
                $query->orderBy('vehicle_type.status', $sortType);
            })
            ->select(
                'vehicle_type.id',
                'vehicle_type.image',
                'vehicle_type.name',
                'vehicle_type.order',
                'vehicle_type.created_at',
                'vehicle_type.status',
            )
            ->with([
                'vehiclePrice' => function ($query) {
                    $query->orderBy('vehicle_distance_price.id', 'asc');
                },
            ]);
    }
}

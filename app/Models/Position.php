<?php

namespace App\Models;

use App\Enums\Types\PositionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'position';
    const ID = 'id';
    const NAME = 'name';
    const PAGE = 'page';
    const PLATFORM_TYPE = 'platform_type';
    const TYPE = 'type';
    const REFERENCE_ID = 'reference_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    public function bannerList()
    {
        return $this->hasMany(BannerPosition::class, 'position_id', 'id')
            ->join('banner', 'banner.id', 'banner_position.banner_id');
    }

    public function videoList()
    {
        return $this->hasMany(VideoPosition::class, 'position_id', 'id')
            ->join('video', 'video.id', 'video_position.video_id');
    }

    //Lists
    public static function lists($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $positionId = isset($filter['position_id']) ? $filter['position_id'] : null;
        $positionName = isset($filter['position']) ? $filter['position'] : null;
        $page = isset($filter['page']) ? $filter['page'] : null;
        $platformType = isset($filter['platform_type']) ? $filter['platform_type'] : null;
        $type = isset($filter['type']) ? $filter['type'] : null;
        $referenceId = isset($filter['reference_id']) ? $filter['reference_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortPage = $sortBy == 'page' ? 'page' : null;
        $sortPosition = $sortBy == 'position' ? 'position' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortStatus = $sortBy == 'status' ? 'status' : null;

        return self::when($positionId, function ($query) use ($positionId) {
            $query->where('position.id', $positionId);
        })
            ->when($page, function ($query) use ($page) {
                $query->where('position.page', $page);
            })
            ->when($platformType, function ($query) use ($platformType) {
                $query->where('position.platform_type', $platformType);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('position.type', $type);
            })
            ->when($positionName, function ($query) use ($positionName) {
                $query->where('position.name', $positionName);
            })
            ->when($referenceId, function ($query) use ($referenceId) {
                $query->where('position.reference_id', $referenceId);
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 2) {
                    $query->where('position.status', PositionStatus::getDisable());
                } else {
                    $query->where('position.status', PositionStatus::getEnable());
                }
            })
            ->when($search, function ($query) use ($search) {
                $query->where('position.name', 'LIKE', '%' . $search . '%');
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('position.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortPage, function ($query) use ($sortType) {
                $query->orderBy('position.page', $sortType);
            })
            ->when($sortPosition, function ($query) use ($sortType) {
                $query->orderBy('position.name', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('position.created_at', $sortType);
            })
            ->when($sortStatus, function ($query) use ($sortType) {
                $query->orderBy('position.status', $sortType);
            })
            ->whereNull('position.deleted_at')
            ->select(
                'position.*'
            )
            ->orderBy('position.id', 'DESC');
    }

    //set data
    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::PAGE} = $data[self::PAGE];
        $this->{self::PLATFORM_TYPE} = $data[self::PLATFORM_TYPE];
        $this->{self::TYPE} = $data[self::TYPE];
        $this->{self::REFERENCE_ID} = $data[self::REFERENCE_ID];
        $this->{self::STATUS} = $data[self::STATUS];
    }
}

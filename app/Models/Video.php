<?php

namespace App\Models;

use App\Enums\Types\PositionStatus;
use App\Enums\Types\PositionType;
use App\Enums\Types\VideoStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Video extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'video';
    const ID = 'id';
    const NAME = 'name';
    const URL = 'url';
    const ORDER = 'order';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //List Admin
    public static function lists($filter = [] , $sortBy = '' , $sortType = 'desc')
    {
        // filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        // sort
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortUrl  = $sortBy == 'url'  ? 'url'  : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortOrder = $sortBy == 'order' ? 'order' : null;
        $sortStatus = $sortBy == 'status' ? 'status' : null;

        return self::when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('video.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('video.url', 'LIKE', '%' . $search . '%');
            });
        })
        ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
            $query->whereBetween('video.created_at', [$createdAtStartDate, $createdAtEndDate]);
        })
        ->when($status, function ($query) use ($status) {
            if ($status == 2) {
                $query->where('video.status', VideoStatus::getDisable());
            } else {
                $query->where('video.status', $status);
            }
        })
        ->when($sortName, function ($query) use ($sortType) {
            $query->orderBy('video.name', $sortType);
        })
        ->when($sortUrl, function ($query) use ($sortType) {
            $query->orderBy('video.url', $sortType);
        })
        ->when($sortCreatedAt, function ($query) use ($sortType) {
            $query->orderBy('video.created_at', $sortType);
        })
        ->when($sortOrder, function ($query) use ($sortType) {
            $query->orderBy('video.order', $sortType);
        })
        ->when($sortStatus, function ($query) use ($sortType) {
            $query->orderBy('video.status', $sortType);
        })
        ->select(
            'video.*',
            DB::raw("'false' as selected")
        )
        ->orderBy('video.order', 'asc');
    }

    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::URL} = $data[self::URL];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    //List For Api
    public static function getVideoDisplay($page = null, $position = null, $platform_type, $filter = [])
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('video_position', 'video_position.video_id', 'video.id')
            ->join('position', 'position.id', 'video_position.position_id')
            ->whereNull('video_position.deleted_at')
            ->where('position.type', PositionType::getVideo())
            ->where('position.status', PositionStatus::getEnable())
            ->where('video.status', VideoStatus::getEnable())
            ->where('position.platform_type', $platform_type)
            ->when(!empty($page), function ($query) use ($page) {
                $query->where('position.page', $page);
            })
            ->when(!empty($position), function ($query) use ($position) {
                $query->where('position.name', $position);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('video.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'video.id',
                'video.name',
                'video.url',
            )
            ->orderBy('video_position.order')
            ->groupBy('video_position.id');
    }

    //Get Count Video
    public static function getCountVideo()
    {
        $totalVideo = self::select(DB::raw('COUNT(video.id) as video'))
            ->join('video_position', 'video_position.video_id', 'video.id')
            ->join('position', 'position.id', 'video_position.position_id')
            ->whereNull('video_position.deleted_at')
            ->where('position.type', PositionType::getVideo())
            ->where('position.status', PositionStatus::getEnable())
            ->where('video.status', VideoStatus::getEnable())
            ->first();

        return $totalVideo->video;
    }
}

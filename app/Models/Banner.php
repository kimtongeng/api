<?php

namespace App\Models;

use App\Enums\Types\BannerPage;
use App\Enums\Types\BannerStatus;
use App\Enums\Types\BannerType;
use App\Enums\Types\IsResizeImage;
use App\Enums\Types\PositionStatus;
use App\Enums\Types\PositionType;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Banner extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'banner';
    const ID = 'id';
    const NAME = 'name';
    const TYPE = 'type';
    const DESCRIPTION = 'description';
    const PLATFORM_TYPE = 'platform_type';
    const IMAGE_TYPE = 'image_type';
    const IMAGE = 'image';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //List Admin
    public static function lists($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $banner_type = isset($filter['banner_type']) ? $filter['banner_type'] : null;
        $image_type = isset($filter['image_type']) ? $filter['image_type'] : null;
        $platform_type = isset($filter['platform_type']) ? $filter['platform_type'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;
        $description = isset($filter['description']) ? $filter['description'] : null;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortName = $sortBy == 'name' ? 'name' : null;
        $sortImageType = $sortBy == 'image_type' ? 'image_type' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortStatus = $sortBy == 'status' ? 'status' : null;

        return self::when($image_type, function ($query) use ($image_type) {
            $query->where('banner.image_type', $image_type);
        })
            ->when($description, function ($query) use ($description) {
                $query->where('banner.description', $description);
            })
            ->when($platform_type, function ($query) use ($platform_type) {
                $query->where('banner.platform_type', $platform_type);
            })
            ->when($banner_type, function ($query) use ($banner_type) {
                $query->where('banner.type', $banner_type);
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 2) {
                    $query->where('banner.status', BannerStatus::getDisable());
                } else {
                    $query->where('banner.status', $status);
                }
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('banner.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('banner.description', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('banner.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortName, function ($query) use ($sortType) {
                $query->orderBy('banner.name', $sortType);
            })
            ->when($sortImageType, function ($query) use ($sortType) {
                $query->orderBy('banner.image_type', $sortType);
            })
            ->when($sortCreatedAt, function($query) use ($sortType){
                $query->orderBy('banner.created_at', $sortType);
            })
            ->when($sortStatus, function ($query) use ($sortType) {
                $query->orderBy('banner.status', $sortType);
            })
            ->select(
                'banner.*',
                'banner.id as id',
                DB::raw("'false' as selected")
            )
            ->orderBy('banner.id', 'DESC');
    }

    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::TYPE} = $data[self::TYPE];
        $this->{self::PLATFORM_TYPE} = $data[self::PLATFORM_TYPE];
        $this->{self::IMAGE_TYPE} = $data[self::IMAGE_TYPE];
        $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    //List For Api
    public static function getBannerDisplay($page = null, $position = null, $reference_id = null, $platform_type)
    {
        return self::join('banner_position', 'banner_position.banner_id', 'banner.id')
            ->join('position', 'position.id', 'banner_position.position_id')
            ->leftjoin('category', 'category.id', 'banner.description')
            ->whereNull('banner_position.deleted_at')
            ->where('position.type', PositionType::getBanner())
            ->where('position.status', PositionStatus::getEnable())
            ->where('banner.status', BannerStatus::getEnable())
            ->where('banner.platform_type', $platform_type)
            ->where('position.platform_type', $platform_type)
            ->when(!empty($page), function ($query) use ($page) {
                $query->where('position.page', $page);
            })
            ->when(!empty($position), function ($query) use ($position) {
                $query->where('position.name', $position);
            })
            ->when(!empty($page) && !empty($reference_id), function ($query) use ($page, $reference_id) {
                if ($page == BannerPage::getRealEstateByPropertyType()) {
                    $query->where('position.reference_id', $reference_id);
                }
            })
            ->select(
                'banner.id',
                'banner.name',
                'banner.type',
                'banner.image',
                DB::raw('CASE WHEN banner.description is null THEN "" ELSE banner.description END description'),
                'category.id as category_id',
                'category.business_id',
                'category.parent_id'
            )
            ->orderBy('banner_position.order')
            ->groupBy('banner_position.id')
            ->get();
    }
}

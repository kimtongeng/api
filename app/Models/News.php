<?php

namespace App\Models;

use App\Models\DistrictNews;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'news';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const EVENT_TYPE_ID = 'event_type_id';
    const CONTACT_ID = 'contact_id';
    const COUNTRY_ID = 'country_id';
    const PROVINCE_ID = 'province_id';
    const DISTRICT_ID = 'district_id';
    const COMMUNE_ID = 'commune_id';
    const NAME = 'name';
    const IMAGE = 'image';
    const AUDIO = 'audio';
    const YOUTUBE_LINK = 'youtube_link';
    const ADDRESS = 'address';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';
    const DESCRIPTION = 'description';
    const ACTIVE = 'active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::EVENT_TYPE_ID} = $data[self::EVENT_TYPE_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        isset($data[self::COUNTRY_ID]) && $this->{self::COUNTRY_ID} = $data[self::COUNTRY_ID];
        isset($data[self::PROVINCE_ID]) && $this->{self::PROVINCE_ID} = $data[self::PROVINCE_ID];
        isset($data[self::DISTRICT_ID]) && $this->{self::DISTRICT_ID} = $data[self::DISTRICT_ID];
        isset($data[self::COMMUNE_ID]) && $this->{self::COMMUNE_ID} = $data[self::COMMUNE_ID];
        $this->{self::NAME} = $data[self::NAME];
        isset($data[self::YOUTUBE_LINK]) && $this->{self::YOUTUBE_LINK} = $data[self::YOUTUBE_LINK];
        isset($data[self::ADDRESS]) && $this->{self::ADDRESS} = $data[self::ADDRESS];
        isset($data[self::LATITUDE]) && $this->{self::LATITUDE} = $data[self::LATITUDE];
        isset($data[self::LONGITUDE]) && $this->{self::LONGITUDE} = $data[self::LONGITUDE];
        isset($data[self::DESCRIPTION]) && $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
    }

    /*
     * Relationship Area
     * */
    //Gallery Photo Relationship
    public function galleryPhoto()
    {
        return $this->hasMany(GalleryPhoto::class, GalleryPhoto::TYPE_ID, GalleryPhoto::ID);
    }
    //Event Type Relationship
    public function eventTypeNewsList()
    {
        return $this->hasMany(EventTypeNews::class, EventTypeNews::BUSINESS_ID, News::ID)
            ->join('business_category', 'business_category.id', 'event_type_news.event_type_id');
    }
    //Position Group Relationship
    public function positionGroupNewsList()
    {
        return $this->hasMany(PositionGroupNews::class, PositionGroupNews::BUSINESS_ID, News::ID)
            ->join('business_category', 'business_category.id', 'position_group_news.position_group_id');
    }
    //Province List Relationship
    public function provinceNewsList()
    {
        return $this->hasMany(ProvinceNews::class, ProvinceNews::BUSINESS_ID, News::ID)
            ->join('province', 'province.id', 'province_news.province_id');
    }
    //News Visitors Relationship
    public function newsVisitors()
    {
        return $this->hasMany(NewsVisitors::class, NewsVisitors::NEWS_ID, self::ID)
            ->join('contact', 'contact.id', 'news_visitors.contact_id');
    }
    //News District Relationship
    public function districtNewsList()
    {
        return $this->hasMany(DistrictNews::class, DistrictNews::NEWS_ID, self::ID)
            ->join('district', 'district.id', 'district_news.district_id');
    }

    public static function listNews($filter = [], $sortBy = "")
    {
        //Filter
        $newsID = isset($filter['news_id']) ? $filter['news_id'] : null;
        $currentUserID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $isAdminRequest = isset($filter['is_admin_request']) ? $filter['is_admin_request'] : null;

        //Sort
        $mostView = $sortBy == 'most_view' ? 'most_view' : null;
        $mostLike = $sortBy == 'most_like' ? 'most_like' : null;

        return self::join('contact', 'contact.id', 'news.contact_id')
        ->join('business_category', 'business_category.id', 'news.event_type_id')
        ->where('news.business_type_id', BusinessTypeEnum::getNews())
        ->when($currentUserID, function ($query) use ($currentUserID) {
            $query->where('news.contact_id', $currentUserID);
        })
        ->when($newsID, function ($query) use ($newsID) {
            $query->where('news.id', $newsID);
        })
        ->when(empty($isAdminRequest), function ($query) use ($mostLike) {
            $query->leftjoin(
                DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getNews() . "' ) favorite"),
                function ($join) {
                    $join->on('news.id', '=', 'favorite.business_id');
                }
            )
            ->addSelect(DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite"))
            ->when($mostLike, function ($query) {
                $query->orderBy("favorite.id", "DESC")
                    ->orderBy("news.id", "DESC");
            });
        })
        ->select(
            'news.id',
            'news.business_type_id',
            'news.contact_id',
            'contact.fullname as contact_name',
            'contact.profile_image as contact_image',
            'news.event_type_id',
            'business_category.name as event_type_name',
            'news.name',
            'news.image',
            'news.audio',
            'news.youtube_link',
            'news.address',
            'news.latitude',
            'news.longitude',
            'news.description',
            'news.created_at'
        )
        ->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getNewsCover())
                    ->orderBy('gallery_photo.order', 'DESC');
            },
            'positionGroupNewsList' => function ($query) {
                $query->select(
                        'position_group_news.*',
                        'position_group_news.business_id as news_id',
                        'business_category.name as position_group_name',
                    );
            },
            'provinceNewsList' => function ($query) {
                $query->select(
                    'province_news.*',
                    'province_news.business_id as news_id',
                    DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
                );
            },
            'districtNewsList' => function ($query) {
                $query->select(
                    'district_news.*',
                    DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
                );
            },
            'newsVisitors' => function ($query) {
                $query->select(
                    'news_visitors.*',
                    'contact.fullname as contact_name',
                    'contact.profile_image as contact_image',
                );
            }
        ])
        ->whereNull('news.deleted_at')
        ->groupBy('news.id')
        ->orderBy('news.id', 'desc');
    }
}

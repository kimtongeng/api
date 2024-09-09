<?php

namespace App\Http\Controllers\Mobile\Modules\SocietySecurity;

use App\Models\News;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\GalleryPhotoType;

class NewsListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //News Detail
    public function getNewsDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.news_id' => 'required|exists:news,id',
        ]);

        $filter = $request->input('filter');

        $data = News::listNews($filter)->first();

        return $this->responseWithData($data);
    }

    //News List For Recipient
    public function getNewsListForRecipient(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
        ]);

        $contactId = $request->input('current_user_id');
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $data = News::join('position_group_news', 'news.id', 'position_group_news.business_id')
            ->join('province_news', 'news.id', 'province_news.business_id')
            ->join('contact', function ($join) {
                $join->on('contact.position_group_id', 'position_group_news.position_group_id')
                ->on('contact.province_id', 'province_news.province_id');
            })
            ->join('contact as poster', 'poster.id', 'news.contact_id')
            ->join('business_category','business_category.id', 'news.event_type_id')
            ->leftjoin('district_news', 'district_news.district_id', 'contact.district_id')
            ->select(
                'news.id',
                'news.business_type_id',
                'news.contact_id',
                'poster.fullname as contact_name',
                'poster.profile_image as contact_image',
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
            ->where('contact.id', $contactId)
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
            ->groupBy('news.id')
            ->orderBy('news.id', 'desc')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

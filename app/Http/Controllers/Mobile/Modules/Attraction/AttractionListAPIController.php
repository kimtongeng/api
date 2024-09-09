<?php


namespace App\Http\Controllers\Mobile\Modules\Attraction;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;

class AttractionListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Attraction Filter Sort
     *
     */
    public function getAttractionFilterSort(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listAttraction($filter, $sort)
            ->where('business.status', BusinessStatus::getApproved())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Attraction Detail
     *
     */
    public function getAttractionDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.attraction_id' => 'required|exists:business,id',
        ]);

        $filter =  $request->input('filter');

        $data =  Business::listAttraction($filter)->first();

        return $this->responseWithData($data);
    }

    /**
     * Get Attraction Detail Place Nearby
     *
     */
    public function getAttractionDetailPlaceNearby(Request $request)
    {
        $this->validate($request, [
            'attraction_id' => 'required|exists:business,id',
        ]);

        $attractionID = $request->input('attraction_id');

        $business = Business::find($attractionID);
        $districtId = null;

        if($business) {
            $districtId = $business->district_id;
        }
        $data = Business::join('contact', 'contact.id', 'business.contact_id')
        ->join('province', 'province.id', 'business.province_id')
        ->join('district', 'district.id', 'business.district_id')
        ->join('business_type', 'business_type.id', 'business.business_type_id')
        ->where("district.id", $districtId)
        ->where('business_type.id', BusinessTypeEnum::getAttraction())
        ->where('business.id', '!=', $attractionID)
        ->select(
            'business.id',
            'business.business_type_id',
            'business.contact_id',
            'contact.fullname as contact_name',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            'business.name',
            'business.image',
            'business.price',
            'business.discount_label',
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.view_count',
            'business.rate_count',
            'business.status',
            'business.created_at',
            'business.description',
        )
        ->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getAttraction())
                    ->orderBy('gallery_photo.order', 'DESC');
            },
            'placeSocialContact' => function ($query) {
                $query->orderBy('place_contact.id', 'DESC');
            },
            'placeVideoList' => function ($query) {
                $query->orderBy('place_video_list.id', 'DESC');
            },
        ])
        ->groupBy('business.id')
        ->get();

        $response = [
            'place_nearby' => $data
        ];

        return $this->responseWithData($response);
    }

    /**
     * Update View Attraction
     */
    public function updateViewAttraction(Request $request)
    {
        $this->validate($request, [
            'attraction_id' => 'required|exists:business,id'
        ]);

        $business = Business::find($request->input('attraction_id'));
        $totalCount = $business->view_count + 1;

        $update = DB::table('business')
            ->where('id', $request->input('attraction_id'))
            ->update([
                'view_count' => $totalCount,
                'updated_at' => Carbon::now()
            ]);

        if ($update) {
            $response = ['view_count' => $totalCount];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

}

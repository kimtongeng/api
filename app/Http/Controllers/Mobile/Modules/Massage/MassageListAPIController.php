<?php

namespace App\Http\Controllers\Mobile\Modules\Massage;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessActive;
use App\Enums\Types\BusinessStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;

class MassageListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Massage Filter Sort
     */
    public function getMassageFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listMassage($filter, $sort)
            ->where('business.status', BusinessStatus::getApproved())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Massage
     */
    public function getMassageDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.massage_id' => 'required|exists:business,id'
        ]);

        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listMassage($filter, $sort)->first();

        return $this->responseWithData($data);
    }

    /**
     * Update View Massage
     */
    public function updateViewMassage(Request $request)
    {
        $this->validate($request, [
            'massage_id' => 'required|exists:business,id'
        ]);

        $business = Business::find($request->input('massage_id'));
        $totalCount = $business->view_count + 1;

        $update = DB::table('business')
        ->where('id', $request->input('massage_id'))
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

    /**
     * Get Massage Detail Nearby Place
     */
    public function getMassageDetailNearbyPlace(Request $request)
    {
        $this->validate($request, [
            'massage_id' => 'required|exists:business,id',
        ]);

        $massageId = $request->input('massage_id');
        $business = Business::find($massageId);
        $communeId = $business->{Business::COMMUNE_ID};

        $data = Business::join('contact', 'contact.id', 'business.contact_id')
            ->join('business_type', 'business_type.id', 'business.business_type_id')
            ->join('province', 'province.id', 'business.province_id')
            ->join('district', 'district.id', 'business.district_id')
            ->join('commune', 'commune.id', 'business.commune_id')
            ->where(function ($query) use ($communeId, $massageId) {
                $query->where('commune.id', $communeId)
                    ->where('business_type.id', BusinessTypeEnum::getMassage())
                    ->where('business.id', '!=', $massageId);
            })
            ->leftjoin(
                DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getMassage() . "' ) favorite"),
                function ($join) {
                    $join->on('business.id', '=', 'favorite.business_id');
                }
            )
            ->select(
                'business.id',
                'business.business_type_id',
                'business_type.name as business_type_name',
                'business.contact_id',
                'contact.fullname as contact_name',
                'province.id as province_id',
                DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
                'district.id as district_id',
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
                'commune.id as commune_id',
                DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as commune_name"),
                'business.name',
                'business.phone',
                'business.description',
                'business.image',
                'business.latitude',
                'business.longitude',
                'business.address',
                'business.price',
                'business.discount_label',
                'business.open_time',
                'business.close_time',
                'business.view_count',
                'business.rate_count',
                'business.status',
                'business.created_at',
                'favorite.id as favorite_id',
                DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite")
            )
            ->whereNull('business.deleted_at')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getMassageCover())
                        ->orderBy('gallery_photo.order', 'DESC');
                },
            ])
            ->groupBy('business.id')
            ->limit(10)
            ->get();

        $response = [
            'nearby_place' => $data
        ];

        return $this->responseWithData($response);
    }
}

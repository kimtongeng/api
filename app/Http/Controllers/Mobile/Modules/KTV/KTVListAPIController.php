<?php

namespace App\Http\Controllers\Mobile\Modules\KTV;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;

class KTVListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get KTV Filter Sort
     */
    public function getKTVFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listKTVs($filter, $sort)
            ->where('business.status', BusinessStatus::getApproved())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get KTV Detail
     *
     */
    public function getKTVDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $filter = $request->input('filter');

        $data = Business::listKTVs($filter)->first();

        return $this->responseWithData($data);
    }

    /**
     * Update View KTV
     */
    public function updateViewKTV(Request $request)
    {
        $this->validate($request, [
            'ktv_id' => 'required|exists:business,id'
        ]);

        $business = Business::find($request->input('ktv_id'));
        $totalCount = $business->view_count + 1;

        $update = DB::table('business')
        ->where('id', $request->input('ktv_id'))
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
     * Get Nearby Place KTV by Commune
     */
    public function getKTVDetailNearbyPlace(Request $request)
    {
        $this->validate($request, [
            'ktv_id' => 'required|exists:business,id',
        ]);

        $ktvID = $request->input('ktv_id');
        $business = Business::find($ktvID);
        $communeId = $business->{Business::COMMUNE_ID};

        $data = Business::join('contact', 'contact.id', 'business.contact_id')
            ->join('business_type', 'business_type.id', 'business.business_type_id')
            ->join('province', 'province.id', 'business.province_id')
            ->join('district', 'district.id', 'business.district_id')
            ->join('commune', 'commune.id', 'business.commune_id')
            ->where(function ($query) use ($communeId, $ktvID) {
                $query->where('commune.id', $communeId)
                    ->where('business_type.id', BusinessTypeEnum::getKtv())
                    ->where('business.id', '!=', $ktvID);
            })
            ->leftjoin(
                DB::raw("(select * from favorite where contact_id = '" . Auth::guard('mobile')->user()->id . "' and business_type_id = '" . BusinessTypeEnum::getKtv() . "' ) favorite"),
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
                'business.discount_label',
                'business.open_time',
                'business.close_time',
                'business.view_count',
                'business.rate_count',
                'business.status',
                'business.created_at',
                'favorite.id as favorite_id',
                DB::raw("CASE WHEN favorite.id IS NULL THEN 'false' ELSE 'true' END is_favorite")
            )->whereNull('business.deleted_at')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getKtvCover())
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

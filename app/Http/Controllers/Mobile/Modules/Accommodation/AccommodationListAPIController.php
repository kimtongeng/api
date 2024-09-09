<?php

namespace App\Http\Controllers\Mobile\Modules\Accommodation;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Enums\Types\AttributeStatus;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;

class AccommodationListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Accommodation Filter & Sort List
     *
     */
    public function getAccommodationFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listAccommodation($filter,$sort)
            ->where('business.status', BusinessStatus::getApproved())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Accommodation Detail
     *
     */
    public function getAccommodationDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.accommodation_id' => 'required|exists:business,id',
        ]);

        $filter = $request->input('filter');

        $data = Business::listAccommodation($filter)->first();

        return $this->responseWithData($data);
    }

    /**
     * Update View Accommodation
     *
     */
    public function updateViewAccommodation(Request $request)
    {
        $this->validate($request, [
            'accommodation_id' => 'required|exists:business,id'
        ]);

        $business = Business::find($request->input('accommodation_id'));
        $totalCount = $business->view_count + 1;

        $update = DB::table('business')
        ->where('id', $request->input('accommodation_id'))
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
     * Get Accommodation Nearby Place
     */
    public function getAccommodationDetailNearbyPlace(Request $request)
    {
        $this->validate($request, [
            'accommodation_id' => 'required|exists:business,id',
        ]);

        $hotelID = $request->input('accommodation_id');
        $business = Business::find($hotelID);
        $districtId = $business->{Business::DISTRICT_ID};
        $typeId = $business->{Business::BUSINESS_CATEGORY_ID};

        $data = Business::join('contact', 'contact.id', 'business.contact_id')
            ->join('business_type', 'business_type.id', 'business.business_type_id')
            ->join('business_category', 'business_category.id', 'business.business_category_id')
            ->join('province', 'province.id', 'business.province_id')
            ->join('district', 'district.id', 'business.district_id')
            ->where(function ($query) use ($hotelID, $districtId, $typeId) {
                $query->where('district.id', $districtId)
                    ->where('business_category.id', $typeId, function ($query) {
                        $query->where('business_type.id', BusinessTypeEnum::getAccommodation());
                    })
                    ->where('business_type.id', BusinessTypeEnum::getAccommodation())
                    ->where('business.id', '!=', $hotelID);
            })
            ->select(
                'business.id',
                'business_type.id as business_type_id',
                'business_type.name as business_type_name',
                'business.contact_id',
                'contact.fullname as contact_name',
                'business.name',
                'business_category.id as business_category_id',
                'business_category.name as business_category_name',
                'province.id as province_id',
                DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
                'district.id as district_id',
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
                'business.image',
                'business.latitude',
                'business.longitude',
                'business.address',
                'business.price',
                'business.view_count',
                'business.rate_count',
                'business.status',
                'business.active as show',
                'business.created_at',
                'business.discount_label',
                'business.description',
                'business.policy',
            )
            ->whereNull('business.deleted_at')
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getAccommodationCover())
                        ->orderBy('gallery_photo.order', 'DESC');
                },
                'businessAttribute' => function ($query) {
                    $query->select(
                        'business_attribute.id',
                        'business_attribute.business_id',
                        'attribute.id as attribute_id',
                        'attribute.name',
                        'attribute.image',
                    )
                        ->where('attribute.status', AttributeStatus::getEnabled())
                        ->orderBy('business_attribute.id', 'DESC')
                        ->get();
                }
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

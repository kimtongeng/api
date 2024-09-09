<?php


namespace App\Http\Controllers\Mobile\Modules\RealEstate;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\PropertyAssetActive;

class PropertyListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Property Filter Sort
     *
     */
    public function getPropertyFilterSort(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listProperty($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Property Detail
     *
     */
    public function getPropertyDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.property_id' => 'required|exists:business,id',
        ]);

        $filter = $request->input('filter');

        $data = Business::listProperty($filter)->first();

        return $this->responseWithData($data);
    }

    /**
     * Update View Property
     */
    public function updateViewProperty(Request $request)
    {
        $this->validate($request, [
            'property_id' => 'required|exists:business,id'
        ]);

        $business = Business::find($request->input('property_id'));
        $totalCount = $business->view_count + 1;

        $update = DB::table('business')
            ->where('id', $request->input('property_id'))
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
     * Get Property List Nearby Place
     */
    public function getPropertyDetailNearbyPlace(Request $request)
    {
        $this->validate($request, [
            'property_id' => 'required|exists:business,id',
        ]);

        $propertyID = $request->input('property_id');
        $business = Business::find($propertyID);
        $propertyTypeID = $business->{Business::PROPERTY_TYPE_ID};
        $districtID = $business->{Business::DISTRICT_ID};

        $data = Business::join('contact','contact.id', 'business.contact_id')
        ->join('business_type', 'business_type.id', 'business.business_type_id')
        ->join('property_type', 'property_type.id', 'business.property_type_id')
        ->join('province', 'province.id', 'business.province_id')
        ->join('district', 'district.id', 'business.district_id')
        ->join('commune', 'commune.id', 'business.commune_id')
        ->leftjoin('contact as sale_assistance', 'sale_assistance.id', 'business.sale_assistance_id')
        ->where(function ($query) use ($propertyID, $propertyTypeID, $districtID) {
            $query->where('district.id', $districtID)
                ->where('property_type.id', $propertyTypeID)
                ->where('business_type.id', BusinessTypeEnum::getProperty())
                ->where('business.id', '!=' , $propertyID);
        })
        ->select(
            'business.id',
            'business.business_type_id',
            'property_type.id as property_type_id',
            'property_type.name as property_type_name',
            'property_type.type as property_type',
            'business.contact_id',
            'contact.fullname as owner_name',
            'contact.profile_image as owner_profile',
            'province.id as province_id',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
            'district.id as district_id',
            DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            'commune.id as commune_id',
            DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as commune_name"),
            'business.code',
            'business.name',
            'business.image',
            'business.price',
            'business.youtube_link',
            'business.phone',
            'business.telegram_number',
            'business.telegram_qr_code',
            'business.view_count',
            'business.latitude',
            'business.longitude',
            'business.address',
            'business.description',
            'business.payment_policy',
            'business.project_development',
            'business.sale_assistance_id',
            'sale_assistance.fullname as sale_assistance_name',
            'sale_assistance.code as sale_assistance_code',
            'sale_assistance.profile_image as sale_assistance_profile',
            'business.sale_assistance_commission',
            'business.sale_assistance_commission_type',
            'business.agency_commission',
            'business.agency_commission_type',
            'business.ref_agency_commission',
            'business.ref_agency_commission_type',
            'business.status',
            'business.created_at'
        )->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getProperty())
                    ->orderBy('gallery_photo.order', 'DESC');
            },
            'propertyAsset' => function ($query) {
                $query->select(
                    'property_asset.*',
                    'asset_category.name as asset_category_name'
                )
                    ->where('property_asset.active', PropertyAssetActive::getTrue())
                    ->orderBy('property_asset.id', 'DESC')
                    ->get();
            }
        ])
        ->whereNull('business.deleted_at')
        ->groupBy('business.id')
        ->limit(10)
        ->get();

        $response = [
            'nearby_place' => $data
        ];

        return $this->responseWithData($response);
    }
}

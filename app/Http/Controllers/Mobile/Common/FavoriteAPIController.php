<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\BusinessTypeEnum;
use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Favorite;
use App\Models\News;
use Illuminate\Http\Request;

class FavoriteAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Add To Favorite
     */
    public function addFavorite(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'business_type_id' => 'required|exists:business_type,id',
            'business_id' => 'required|exists:business,id',
        ]);

        $request->merge([Favorite::CONTACT_ID => $request->input('current_user_id')]);

        $favoriteCount = Favorite::where(Favorite::CONTACT_ID, $request->input(Favorite::CONTACT_ID))
            ->where(Favorite::BUSINESS_TYPE_ID, $request->input(Favorite::BUSINESS_TYPE_ID))
            ->where(Favorite::BUSINESS_ID, $request->input(Favorite::BUSINESS_ID))
            ->get();

        if (count($favoriteCount) > 0) {
            return response()->json([
                'message' => 'This has already been taken.'
            ], 422);
        } else {
            $favorite = new Favorite();

            $favorite->setData($request);

            if ($favorite->save()) {
                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }
    }

    /**
     * Remove From Favorite
     */
    public function removeFavorite(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'business_type_id' => 'required|exists:business_type,id',
            'business_id' => 'required|exists:business,id'
        ]);

        $request->merge([Favorite::CONTACT_ID => $request->input('current_user_id')]);

        $favorite = Favorite::where(Favorite::CONTACT_ID, $request->input(Favorite::CONTACT_ID))
            ->where(Favorite::BUSINESS_TYPE_ID, $request->input(Favorite::BUSINESS_TYPE_ID))
            ->where(Favorite::BUSINESS_ID, $request->input(Favorite::BUSINESS_ID));

        if ($favorite->delete()) {
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Get From Favorite
     */
    public function getFavorite(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'business_type_id' => 'required|exists:business_type,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $businessType = $request->input(Favorite::BUSINESS_TYPE_ID);
        $data = [];

        if ($businessType == BusinessTypeEnum::getProperty()) {
            $data = Business::listProperty()
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        } else if ($businessType == BusinessTypeEnum::getAttraction()) {
            $data = Business::listAttraction()
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        } else if ($businessType == BusinessTypeEnum::getShopWholesale() ||
            $businessType == BusinessTypeEnum::getShopRetail() ||
            $businessType == BusinessTypeEnum::getRestaurant() ||
            $businessType == BusinessTypeEnum::getShopLocalProduct() ||
            $businessType == BusinessTypeEnum::getService() ||
            $businessType == BusinessTypeEnum::getModernCommunity()
        ) {
            $data = Business::listShop(['business_type_id' => $businessType])
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        } else if ($businessType == BusinessTypeEnum::getCharityOrganization()) {
            $data = Business::listCharityOrganization()
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        } else if ($businessType == BusinessTypeEnum::getAccommodation()) {
            $data = Business::listAccommodation()
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        } else if ($businessType == BusinessTypeEnum::getMassage()) {
            $data = Business::listMassage()
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        } else if ($businessType == BusinessTypeEnum::getNews()) {
            $data = News::listNews()
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        } else if ($businessType == BusinessTypeEnum::getKtv()) {
            $data = Business::listKTVs()
                ->whereNotNull('favorite.id')
                ->orderBy('favorite.id', 'desc')
                ->addSelect('favorite.id as favorite_id')
                ->paginate($tableSize);
        }

        $response = [
            'pagination' => [
                'total' => intval($data->total()),
                'per_page' => intval($data->perPage()),
                'current_page' => intval($data->currentPage()),
                'last_page' => intval($data->lastPage()),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            'data' => !empty($data) ? $data->items() : []
        ];
        return $this->responseWithData($response);
    }
}

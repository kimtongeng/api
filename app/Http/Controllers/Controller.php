<?php

namespace App\Http\Controllers;

use App\Enums\Types\BusinessTypeEnum;
use App\Models\Lib;
use App\Models\Main\DefaultSetting;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        /** Share Data to All View Blade ***/
        view()->composer('*', function ($view) {

            $current_users = request()->user();

            $view->with([
                'current_users' => $current_users,
            ]);
        });
    }

    public function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null
        ], 200);
    }

    public function responseWithData($data = [])
    {
        return response()->json([
            'data' => $data,
            'success' => 1,
            'message' => 'Your action has been completed successfully.'
        ], 200);
    }

    public function responseWithPagination($data)
    {
        $response = [
            'pagination' => [
                'total' => intval($data->total()),
                'per_page' => intval($data->perPage()),
                'current_page' => intval($data->currentPage()),
                'last_page' => intval($data->lastPage()),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            'data' => $data->items()
        ];
        return $this->responseWithData($response);
    }

    public function responseValidation($errors)
    {
        return response()->json($errors, 424);
    }

    public function responseNoPermission()
    {
        return response()->json(['success' => 0, 'message' => Lib::PER_FAIL], 403);
    }

    public function responseWithSuccess()
    {
        return response()->json(['success' => 1, 'message' => 'Your action has been completed successfully'], 200);
    }

    public function responseImagePath($business_type)
    {
        $image_path = '';

        if ($business_type == BusinessTypeEnum::getAccommodation()) {
            $image_path = '/accommodation/logo/';
        } else if ($business_type == BusinessTypeEnum::getShopRetail() ||
            $business_type == BusinessTypeEnum::getShopWholesale() ||
            $business_type == BusinessTypeEnum::getShopLocalProduct() ||
            $business_type == BusinessTypeEnum::getRestaurant() ||
            $business_type == BusinessTypeEnum::getService() ||
            $business_type == BusinessTypeEnum::getModernCommunity()
        ) {
            $image_path = '/shop/logo/';
        } else if ($business_type == BusinessTypeEnum::getMassage()) {
            $image_path = '/massage/logo/';
        } else if ($business_type == BusinessTypeEnum::getProperty()) {
            $image_path = '/real_estate/property/thumbnail/';
        } else if ($business_type == BusinessTypeEnum::getAttraction()) {
            $image_path = '/attraction/thumbnail/';
        } else if ($business_type == BusinessTypeEnum::getKtv()) {
            $image_path = '/ktv/logo/';
        } else if ($business_type == BusinessTypeEnum::getCharityOrganization()) {
            $image_path = '/charity/organization_logo/';
        }

        return $image_path;
    }
}

<?php

namespace App\Http\Controllers\Mobile\Modules\Shop;

use App\Enums\Types\BusinessStatus;
use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Enums\Types\IsDiscount;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\IsFreeDelivery;
use App\Http\Controllers\Controller;

class ShopListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Shop Popular List
     *
     */
    public function getShopPopularList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [];
        $sort = 'most_like';

        $data = Business::listShop($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Shop Discount List
     *
     */
    public function getShopDiscountList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = ['is_discount' => IsDiscount::getYes()];
        $sort = 'newest';

        $data = Business::listShop($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Shop Free Delivery List
     *
     */
    public function getShopDeliveryList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = ['is_free_delivery' => IsFreeDelivery::getYes()];
        $sort = 'newest';

        $data = Business::listShop($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Shop Newest List
     *
     */
    public function getShopNewestList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [];
        $sort = 'newest';

        $data = Business::listShop($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Shop Filter Sort List
     *
     */
    public function getShopFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
        ]);
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listShop($filter, $sort)
            ->where('business.status', BusinessStatus::getApproved())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Update View Accommodation
     *
     */
    public function updateViewShop(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:business,id'
        ]);

        $business = Business::find($request->input('shop_id'));
        $totalCount = $business->view_count + 1;

        $update = DB::table('business')
        ->where('id', $request->input('shop_id'))
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
     * Shop Get Detail
     *
     */
    public function getShopDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.shop_id' => 'required|exists:business,id',
        ]);

        $filter =  $request->input('filter');

        $data = Business::listShop($filter)->first();

        return $this->responseWithData($data);
    }

    /**
     * Shop Filter Transaction List
     *
     */
    public function getShopFilterTransactionList(Request $request)
    {
        $this->validate($request,[
            'filter' => 'required',
            'filter.contact_id' => 'required|exists:contact,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter =  $request->input('filter');

        $data = Business::listShop($filter)
            ->join('transaction', 'business.id', 'transaction.business_id')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

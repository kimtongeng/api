<?php

namespace App\Http\Controllers\Admin\Modules\Business\Shop;

use App\Models\Contact;
use App\Models\UserLog;
use App\Models\Business;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessActive;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BusinessBankAccount;
use App\Models\BusinessCategory;
use App\Models\Category;
use App\Models\Modifier;
use App\Models\Product;

class ShopListController extends Controller
{
    const MODULE_KEY = 'shop_list';

    public function get(Request $request)
    {

        if(Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = $this->getList(
                $request->input('table_size'),
                $request->input('filter'),
                $request->input('sort_by'),
                $request->input('sort_type')
            );
            return $this->responseWithData($data);

        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Get List
     *
     */
    public function getList($tableSize, $filter = [], $sortBy = '' , $sortType = '')
    {
        if(empty($tableSize)){
            $tableSize = 10;
        }

        $filter['is_admin_request'] = true;
        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = Business::listShop($filter,$sortBy,$sortType)
            ->addSelect(
                DB::raw("
                    CASE WHEN business.active = '" . BusinessActive::getTrue() . "'
                    THEN 'true'
                    ELSE 'false'
                    END active
                "),
                DB::raw("
                    CASE WHEN business.verify = '" . BusinessActive::getTrue() . "'
                    THEN 'true'
                    ELSE 'false'
                    END is_verify
                "),
            )
            ->paginate($tableSize);
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            'data' => $data->items(),
        ];
        return $response;
    }

    /**
     * Change Active
     *
     */
    public function changeActive(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:business,id',
            'active' => 'required'
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input('id'));
        $business->active = $request->input('active');
        if ($business->save()) {
            $description = ' Id : ' . $business->id . ', Change Active To: ' . $business->active;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Update Verify
     *
     */
    public function updateVerify(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:business,id',
            'verify' => 'required'
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input('id'));
        $business->verify = $request->input('verify');
        if ($business->save()) {
            $description = ' Id : ' . $business->id . ', Update Verify To: ' . $business->verify;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get Shop Detail
     *
     */
    public function getDetail(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, 'detail')) {
            $this->validate($request, [
                'shop_id' => 'required|exists:business,id',
            ]);

            $filter['shop_id'] = $request->input('shop_id');
            $filter['is_admin_request'] = true;

            $data = Business::listShop($filter)
                ->first();

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Get Product List
     *
     */
    public function getProductList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 3 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Product::lists($filter,$sort)
        ->addSelect(
            DB::raw("
                CASE WHEN product.status = '" . ProductStatus::getEnabled() ."'
                THEN 'true'
                WHEN product.status = '" . ProductStatus::getSuspend() . "'
                THEN 'false'
                END suspend
            ")
        )
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Select Data
     *
     */
    public function getSelectData(Request $request)
    {
        $businessOwner = Contact::getContactHasBusinessList(['business_type' => BusinessTypeEnum::getComboListShop()]);

        $businessCategory = BusinessCategory::listsAdmin()
        ->where(function ($query) {
                $query->where('business_category.business_type_id', BusinessTypeEnum::getShopRetail())
                    ->orWhere('business_category.business_type_id', BusinessTypeEnum::getShopWholesale())
                    ->orWhere('business_category.business_type_id', BusinessTypeEnum::getRestaurant())
                    ->orWhere('business_category.business_type_id', BusinessTypeEnum::getShopLocalProduct())
                    ->orWhere('business_category.business_type_id', BusinessTypeEnum::getService())
                    ->orWhere('business_category.business_type_id', BusinessTypeEnum::getModernCommunity());
            })
            ->addSelect(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(business_category.name, '$.en_US')) as category_name"),
            )
            ->get();

        $response = [
            'business_owner' => $businessOwner,
            'business_category' => $businessCategory,
        ];
        return $this->responseWithData($response);
    }

    /**
     * Get Select Data Shop Detail
     *
     */
    public function getSelectShopDetail(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
        ]);
        $filter['business_id'] = $request->input('business_id');

        $categoryData = Category::listCategory($filter)
        ->whereNull('category.parent_id')->get();
        $brandData = Brand::listBrand($filter)->get();
        $modifierData = Modifier::lists($filter)->get();
        $bankAccount = BusinessBankAccount::where('business_id', $filter['business_id'])
        ->leftJoin('bank_account','bank_account.id','business_bank_account.bank_account_id')
        ->leftJoin('bank','bank.id','bank_account.bank_id')
        ->select(
            'bank.name as bank_name',
            'bank.image as bank_image',
            'bank_account.account_name',
            'bank_account.account_number',
            'bank_account.account_qr_code',
        )
        ->get();

        $response = [
            'category' => $categoryData,
            'brand' => $brandData,
            'modifier' => $modifierData,
            'bank_account' => $bankAccount
        ];
        return $this->responseWithData($response);
    }

    /**
     *
     * Change Status Product
     */
    public function changeStatusSuspend(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:product,id',
            'suspend' => 'required'
        ]);

        DB::beginTransaction();

        $product = Product::find($request->input('id'));
        $product->status = $request->input('suspend');
        if ($product->save()) {
            $description = ' Id : ' . $product->id . ', Change Status To: ' . $product->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }
}

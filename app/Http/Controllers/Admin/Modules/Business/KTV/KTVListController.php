<?php

namespace App\Http\Controllers\Admin\Modules\Business\KTV;

use App\Models\Room;
use App\Models\Contact;
use App\Models\Product;
use App\Models\UserLog;
use App\Models\Business;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\BusinessStaff;
use App\Enums\Types\ProductStatus;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessActive;
use App\Models\BusinessBankAccount;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;

class KTVListController extends Controller
{
    const MODULE_KEY = 'ktv_list';

    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
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
    private function getList($tableSize, $filter = [], $sortBy = '', $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $filter['is_admin_request'] = true;
        $filter['created_at_range'] = $filter['date_time_picker'];
        $data = Business::listKTVs($filter, $sortBy, $sortType)
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
     * Get Detail
     *
     */
    public function getDetail(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, 'detail')) {
            $this->validate($request, [
                'ktv_id' => 'required|exists:business,id',
            ]);

            $filter['business_id'] = $request->input('ktv_id');
            $filter['is_admin_request'] = true;

            $data = Business::listKTVs($filter)
                ->with([
                    'relatedDocument' => function ($query) {
                        $query->orderBy('related_document.order', 'ASC');
                    },
                ])
                ->first();

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Get Select Data
     *
     */
    public function getSelectData(Request $request)
    {
        $businessOwner = Contact::getContactHasBusinessList(['business_type' => BusinessTypeEnum::getKtv()]);

        $response = [
            'business_owner' => $businessOwner,
        ];
        return $this->responseWithData($response);
    }

    /**
     * Get Massage Detail
     *
     */
    public function getSelectKTVDetail(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
        ]);
        $filter['business_id'] = $request->input('business_id');

        $bankAccount = BusinessBankAccount::where('business_id', $filter['business_id'])
        ->leftJoin('bank_account', 'bank_account.id', 'business_bank_account.bank_account_id')
        ->leftJoin('bank', 'bank.id', 'bank_account.bank_id')
        ->select(
            'bank.name as bank_name',
            'bank.image as bank_image',
            'bank_account.account_name',
            'bank_account.account_number',
            'bank_account.account_qr_code',
        )
        ->get();

        $response = [
            'bank_account' => $bankAccount
        ];
        return $this->responseWithData($response);
    }

    /**
     * Get Service List
     */
    public function getFoodList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 3 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Product::listsKTV($filter, $sort)
            ->addSelect(
                DB::raw("
                CASE WHEN product.status = '" . ProductStatus::getEnabled() . "'
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
     * Get Massage Therapist
     */
    public function getKTVGirlList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = BusinessStaff::listsKTVGirl($filter, $sort)->get();

        return $this->responseWithData($data);
    }

    /**
     * Get Room KTV List
     *
     */
    public function getRoomKTVList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 3 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Room::listsKTVRoom($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
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
}

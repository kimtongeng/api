<?php

namespace App\Http\Controllers\Admin\Modules\Business;

use App\Enums\Types\BusinessActive;
use App\Enums\Types\BusinessStatus;
use App\Enums\Types\BusinessTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Contact;
use App\Models\Permission;
use App\Models\PropertyAsset;
use App\Models\PropertyType;
use App\Models\Province;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttractionListController extends Controller
{
    const MODULE_KEY = 'attraction';


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
        $data = Business::listAttraction($filter, $sortBy, $sortType)
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
     * Get Property Detail
     *
     */
    public function getDetail(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, 'detail')) {
            $this->validate($request, [
                'attraction_id' => 'required|exists:business,id',
            ]);

            $filter['is_admin_request'] = true;
            $filter['attraction_id'] = $request->input('attraction_id');

            $data = Business::listAttraction($filter)->first();

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function getSelectData(Request $request)
    {
        $businessOwner = Contact::getContactHasBusinessList(['business_type' => BusinessTypeEnum::getAttraction()]);
        $province = Province::lists()->get();

        $response = [
            'business_owner' => $businessOwner,
            'province' => $province
        ];
        return $this->responseWithData($response);
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

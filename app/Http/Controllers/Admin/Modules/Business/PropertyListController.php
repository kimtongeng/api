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

class PropertyListController extends Controller
{
    const MODULE_KEY = 'property';


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
        $data = Business::listProperty($filter, $sortBy, $sortType)
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
                'property_id' => 'required|exists:business,id',
            ]);

            $filter['property_id'] = $request->input('property_id');
            $filter['is_admin_request'] = true;

            $data = Business::listProperty($filter)
                ->with([
                    'relatedDocument' => function ($query) {
                        $query->orderBy('related_document.order', 'ASC');
                    },
                    'idCardImage' => function ($query) {
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
     * Get Asset List
     *
     */
    public function getAssetList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.property_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 3 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = PropertyAsset::lists($filter, $sort)
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    public function getSelectData(Request $request)
    {
        $businessOwner = Contact::getContactHasBusinessList(['business_type' => BusinessTypeEnum::getProperty()]);
        $province = Province::lists()->get();

        $response = [
            'business_owner' => $businessOwner,
            'property_type' => PropertyType::lists()->get(),
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

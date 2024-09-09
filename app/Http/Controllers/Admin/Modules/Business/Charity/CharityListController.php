<?php

namespace App\Http\Controllers\Admin\Modules\Business\Charity;

use App\Models\Contact;
use App\Models\UserLog;
use App\Models\Business;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessActive;
use App\Enums\Types\BusinessStatus;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;

class CharityListController extends Controller
{
    const MODULE_KEY = 'charity_donor';


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

    public function getList($tableSize, $filter = [], $sortBy = '', $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $filter['is_admin_request'] = true;
        $filter['created_at_range'] = $filter['date_time_picker'];
        $data = Business::listCharityOrganization($filter,$sortBy,$sortType)
        ->addSelect(
            DB::raw("
                CASE WHEN business.status = '" . BusinessStatus::getApproved() . "'
                THEN 'true'
                WHEN business.status = '" . BusinessStatus::getPending() . "'
                THEN 'false'
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
     * Get Charity Detail
     *
     */
    public function getDetail(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, 'detail')) {
            $this->validate($request, [
                'charity_id' => 'required|exists:business,id',
            ]);

            $filter['is_admin_request'] = true;
            $filter['organization_id'] = $request->input('charity_id');

            $data = Business::listCharityOrganization($filter)
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

    public function changeActive(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:business,id',
            'active' => 'required'
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input('id'));
        $business->status = $request->input('active');
        if ($business->save()) {
            $description = ' Id : ' . $business->id . ', Change Active To: ' . $business->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    public function getSelectData(Request $request)
    {
        $businessOwner = Contact::getContactHasBusinessList(['business_type' => BusinessTypeEnum::getCharityOrganization()]);
        $charity_type = BusinessCategory::listsAdmin(['business_type_id' => BusinessTypeEnum::getCharityOrganization()])->get();

        $response = [
            'business_owner' => $businessOwner,
            'charity_type' => $charity_type,
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

<?php

namespace App\Http\Controllers\Admin\Modules\Business\Accommodation;

use App\Models\Room;
use App\Models\Contact;
use App\Models\UserLog;
use App\Models\Business;
use App\Models\Province;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessActive;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;

class AccommodationListController extends Controller
{
    const MODULE_KEY = 'accommodation_list';

    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = $this->getList($request->input('table_size'),
                $request->input('filter'),
                $request->input('sort_by'),
                $request->input('sort_type')
            );
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    private function getList($tableSize, $filter = [], $sortBy = '', $sortType)
    {
        if(empty($tableSize)) {
            $tableSize = 10;
        }

        $filter['is_admin_request'] = true;
        $filter['created_at_range'] = $filter['date_time_picker'];
        $data = Business::listAccommodation($filter, $sortBy, $sortType)
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

    //get detail
    public function getDetail(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, 'detail')) {
            $this->validate($request, [
                'accommodation_id' => 'required|exists:business,id',
            ]);

            $filter['is_admin_request'] = true;
            $filter['accommodation_id'] = $request->input('accommodation_id');

            $data = Business::listAccommodation($filter)->first();

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Get Product List
     *
     */
    public function getRoomList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $filter = $request->input('filter');

        $data = Room::lists($filter)
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    public function getSelectData(Request $request)
    {
        $businessOwner = Contact::getContactHasBusinessList(['business_type' => BusinessTypeEnum::getAccommodation()]);
        $hotel_type = BusinessCategory::listsAdmin(['business_type_id' => BusinessTypeEnum::getAccommodation()])->get();
        $province = Province::lists()->get();

        $response = [
            'business_owner' => $businessOwner,
            'hotel_type' => $hotel_type,
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

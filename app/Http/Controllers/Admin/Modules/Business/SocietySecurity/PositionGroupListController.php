<?php

namespace App\Http\Controllers\Admin\Modules\Business\SocietySecurity;

use Carbon\Carbon;
use App\Models\UserLog;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\BusinessCategoryStatus;
use App\Enums\Types\BusinessCategoryType;

class PositionGroupListController extends Controller
{
    const MODULE_KEY = 'position_group';

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

    private function getList($tableSize, $filter, $sortBy = '', $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $filter['created_at_range'] = $filter['date_time_picker'];
        $data = BusinessCategory::lists($filter, $sortBy, $sortType)
            ->where(function ($query) {
                $query->where('business_category.business_type_id', BusinessTypeEnum::getNews())
                    ->where('business_category.type', BusinessCategoryType::getPositionGroup());
            })
            ->addSelect(
                DB::raw("
                    CASE WHEN business_category.status = '" . BusinessCategoryStatus::getEnabled() . "'
                    THEN 'true'
                    ELSE 'false'
                    END status
                ")
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

    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:business_category,id' : 'nullable',
            'business_type_id' => 'required|exists:business_type,id',
            'name' => 'required',
            'status' => 'required'
        ]);
    }

    //Set Data
    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $business_category = new BusinessCategory();
            $business_category->setData($request);
            $business_category->{BusinessCategory::TYPE} = BusinessCategoryType::getPositionGroup();
            $business_category->{BusinessCategory::CREATED_AT} = Carbon::now();
            $business_category->save();

            DB::commit();

            return $this->responseWithData($business_category);
        } else {
            return $this->responseNoPermission();
        }
    }

    //Update Data
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $business_category = BusinessCategory::find($request->input('id'));
            $business_category->setData($request);
            $business_category->updated_at = Carbon::now();
            $business_category->save();

            DB::commit();

            return $this->responseWithData($business_category);
        } else {
            return $this->responseNoPermission();
        }
    }

    //Delete Data
    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:business_category,id'
            ]);

            DB::beginTransaction();

            $business_category = BusinessCategory::find($request['id']);

            if ($business_category->delete()) {

                BusinessCategory::where(BusinessCategory::ID, $business_category->id)->delete();

                $description = 'Id : ' . $business_category->id . ', Name : ' . $business_category->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }
            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:business_category,id'
        ]);

        DB::beginTransaction();

        $business_category = BusinessCategory::find($request->input('id'));
        $business_category->status = $request->input('status');
        if ($business_category->save()) {
            $description = ' Id : ' . $business_category->id . ', Change Status To: ' . $business_category->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }
}

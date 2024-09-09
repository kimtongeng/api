<?php

namespace App\Http\Controllers\Admin\Modules\Business;

use Carbon\Carbon;
use App\Models\UserLog;
use App\Models\Permission;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeStatus;

class BusinessTypeController extends Controller
{
    const MODULE_KEY = 'business_type';

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
        $data = BusinessType::lists($filter, $sortBy, $sortType)
        ->addSelect(
            DB::raw("
                CASE WHEN business_type.status = '" . BusinessTypeStatus::getEnable() . "'
                THEN 'true'
                WHEN business_type.status = '" . BusinessTypeStatus::getDisable() . "'
                THEN 'false'
                END as `show`
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

    /**
     * Store
     *
     */
    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $business_type = new BusinessType();
            $business_type->setData($request);

            if($business_type->save()) {
                if (!empty($request->input('image'))) {
                    //Upload Image
                    $image = StringHelper::uploadImage($request->input('image'), ImagePath::businessTypeImagePath);
                    $business_type->image = $image;
                    $business_type->save();
                }
            }

            DB::commit();

            return $this->responseWithData($business_type);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * update
     */
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $business_type = BusinessType::find($request->input('id'));
            $business_type->setData($request);
            $business_type->updated_at = Carbon::now();

            if ($business_type->save()) {
                if (!empty($request->input('image'))) {
                    // Update Image
                    $image = StringHelper::editImage(
                        $request->input('image'),
                        $request->input('old_image'),
                        ImagePath::businessTypeImagePath
                    );
                    $business_type->image = $image;
                    $business_type->save();
                }
            }

            DB::commit();

            return $this->responseWithData($business_type);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Delete
     */
    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:business_type,id'
            ]);

            DB::beginTransaction();

            $business_type = BusinessType::find($request->input('id'));

            StringHelper::deleteImage($business_type->image, ImagePath::businessTypeImagePath);

            if ($business_type->delete()) {

                BusinessType::where(BusinessType::ID, $business_type->id)->delete();
            }
            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Check Validation
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:business_type,id' : 'nullable',
            'name' => 'required',
            'has_transaction' => 'required',
            'status' => 'required'
        ]);
    }

    /**
     * Change Status Product
     */
    public function changeStatusBusinessType(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:business_type,id',
            'show' => 'required'
        ]);

        DB::beginTransaction();

        $business_type = BusinessType::find($request->input('id'));
        $business_type->status = $request->input('show');
        if ($business_type->save()) {
            $description = ' Id : ' . $business_type->id . ', Change Status To: ' . $business_type->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get Order Auto
     */
    public function getAutoOrder()
    {
        $lastOrder = 0;

        $data = BusinessType::orderBy('order', 'DESC')->first();
        if (!empty($data)) {
            $lastOrder = $data->order + 1;
        }

        return $this->responseWithData($lastOrder);
    }
}

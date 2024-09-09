<?php

namespace App\Http\Controllers\Admin\Modules\Developer;

use App\Http\Controllers\Controller;
use App\Models\Main\BodyType;
use App\Models\Permission;
use App\Models\Support;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportManagementController extends Controller
{
    const MODULE_KEY = 'support_management';

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

    private function getList($tableSize, $filter , $sortBy = '' , $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }
        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = Support::lists($filter , $sortBy , $sortType)
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

    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $support = new Support();
            $support->setData($request);

            if ($support->save()) {
                // Set Log
                $description = 'ID: ' . $support->id . ', Type : ' . $support->support_type . ', Value : ' . $support->support_value;
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();

            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {

            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $support = Support::find($request['id']);
            $support->setData($request);

            if ($support->save()) {
                $description = 'ID: ' . $support->id . ', Type : ' . $support->support_type . ', Value : ' . $support->support_value;
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }

            DB::commit();

            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required'
            ]);

            DB::beginTransaction();

            $support = Support::find($request['id']);

            if ($support->delete()) {
                $description = 'ID: ' . $support->id . ', Type : ' . $support->support_type . ', Value : ' . $support->support_value;
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }

            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    public function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:support,id' : 'nullable',
            'support_type' => 'required|max:255',
            'support_value' => 'required|max:255',
        ]);
    }
}

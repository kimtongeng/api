<?php

namespace App\Http\Controllers\Admin\Modules\Developer;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\UserLog;
use DB;
use Illuminate\Http\Request;
use function response;

class PermissionController extends Controller
{
    const MODULE_KEY = 'permission';

    public function checkAuthorize(Request $request)
    {
        $moduleKey = $request->input('module_key');
        $action = $request->input('action');
        $isAuthorize = Permission::authorize($moduleKey, $action);
        if ($isAuthorize) {
            return $this->responseWithSuccess();
        }
        return response()->json(['success' => false, 'message' => 'Your action has been completed successfully.'], 403);
    }

    /**
     * Get permission data
     */
    public function getPermission(Request $request)
    {
        $data = Permission::actionType();
        return response()->json(['data' => $data, 'success' => 1, 'message' => 'Permission data granted.'], 200);
    }

    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $tableSize = $request->input('table_size');
            $data = $this->getList($tableSize);
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Store Permission
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            $filter = $request->input('filter');
            $tableSize = isset($filter['table_size']) ? $filter['table_size'] : 10;

            DB::beginTransaction();
            $permission = new Permission();
            $permission->setData($request);
            if ($permission->save()) {
                // Set Log
                $description = 'Id : ' . $permission->id . ', Permission Name : ' . $permission->getName();
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();
            $data = $this->getList($tableSize);
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Update Permission
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $this->checkValidation($request);

            $filter = $request->input('filter');

            DB::beginTransaction();

            $permission = Permission::find($request['id']);
            $permission->setData($request);

            if ($permission->save()) {
                $description = 'Id : ' . $permission->id . ', Permission Name : ' . $permission->getName();
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }
            DB::commit();
            $data = $this->getList($filter['table_size']);
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Delete Permission
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|max:255'
            ]);
            $filter = $request->input('filter');
            DB::beginTransaction();
            $permission = Permission::find($request['id']);
            $description = 'Id : ' . $permission->id . ', Permission Name : ' . $permission->getName();
            if ($permission->delete()) {
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }
            DB::commit();
            $data = $this->getList($filter['table_size']);
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Check validation when insert
     *
     * @param [type] $data
     * @return void
     */
    public function checkValidation($request)
    {
        $this->validate($request, [
            'permission_name' => ['required']
        ]);
    }

    public function lists(Request $request)
    {
        $data = Permission::lists()->get();
        return $this->responseWithData($data);
    }

    private function getList($tableSize)
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }
        $data = Permission::lists()->paginate($tableSize);
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            'data' => $data->items()
        ];
        return $response;
    }
}

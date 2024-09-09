<?php

namespace App\Http\Controllers\Admin\Modules\Setting\User;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleModule;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Validator;
use function response;

class RoleController extends Controller
{
    const MODULE_KEY = 'user_role';

    public function __construct()
    {
        $this->role = new Role();
        $this->roleModule = new RoleModule();
    }

    //get
    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $tableSize = $request->input('table_size');
            $filter = $request->input('filter');
            $sortBy = $request->input('sort_by');
            $sortType = $request->input('sort_type');

            if (empty($tableSize)) {
                $tableSize = 10;
            }

            $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
            $data_roles = Role::getLists($tableSize, $filter , $sortBy, $sortType);
            $response = [
                'pagination' => [
                    'total' => $data_roles->total(),
                    'per_page' => $data_roles->perPage(),
                    'current_page' => $data_roles->currentPage(),
                    'last_page' => $data_roles->lastPage(),
                    'from' => $data_roles->firstItem(),
                    'to' => $data_roles->lastItem()
                ],
                'data' => $data_roles->items(),
                'success' => 1,
                'message' => 'done'
            ];
            return response()->json($response, 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    //Get Module and Permission for New
    public function getModulePermission(Request $request)
    {
        $data = Module::lists()->get();
        return $this->responseWithData($data);
    }

    //Get Module and Permission for update
    public function getUpdate(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $filter = [];
            $roleId = $request->input('role_id');
            $data = [
                'role' => Role::where('id', $roleId)->first(),
                'module_item' => Module::lists()->get(),
                'role_module' => RoleModule::getModulePermissionById($roleId),
            ];
            return $this->responseWithData($data);
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    //store
    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            $this->checkValidation($request);

            $module_items = $request['module_items'];

            DB::beginTransaction();
            $role = new Role();
            $role->setData($request);
            if ($role->save()) {
                foreach ($module_items as $module_item_obj) {
                    foreach ($module_item_obj['permission'] as $permission_item) {
                        if ($permission_item['checked'] == true) {
                            $role_module_data = [
                                RoleModule::ROLE_ID => $role->id,
                                RoleModule::MODULE_ID => $module_item_obj['id'],
                                RoleModule::PERMISSION_ID => $permission_item['id'],
                            ];
                            $role_module = new RoleModule();
                            $role_module->setData($role_module_data);
                            $role_module->save();
                        }
                    }
                }
                $description = 'Created Id : ' . $role->id . ', name : ' . $role->role_name;
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();
        } else {

            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    //update
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $this->checkValidation($request);
            $module_items = $request['module_items'];
            DB::beginTransaction();

            $role = Role::find($request->id);
            $role->setData($request);
            if ($role->save()) {

                //Delete all role_module
                RoleModule::where('role_id', $role->id)->delete();

                //Insert new role_module
                foreach ($module_items as $module_item_obj) {
                    foreach ($module_item_obj['permission'] as $permission_item) {
                        if ($permission_item['checked'] == true) {
                            $role_module_data = [
                                RoleModule::ROLE_ID => $role->id,
                                RoleModule::MODULE_ID => $module_item_obj['id'],
                                RoleModule::PERMISSION_ID => $permission_item['id'],
                            ];
                            $role_module = new RoleModule();
                            $role_module->setData($role_module_data);
                            $role_module->save();
                        }
                    }
                }
                $description = 'Created Id : ' . $role->id . ', name : ' . $role->role_name;
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }

            DB::commit();
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    //getByUserType
    public function getByUserType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Validation failed.', 'errors' => $validator->messages()], 422);
        }
        $userTypeId = $request->input('user_type_id');
        $data = $this->role->getByUserType($userTypeId);
        return response()->json(['data' => $data, 'success' => 1, 'message' => 'done'], 200);
    }

    //Delete role
    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $data = $request->input('data');
            $validator = Validator::make($data, [
                'role_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => 0, 'message' => 'Validation failed.', 'errors' => $validator->messages()], 422);
            }
            $roleId = $data['role_id'];

            DB::beginTransaction();

            RoleModule::where('role_id', $roleId)->delete();
            Role::where('id', $roleId)->delete();

            UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), 'Deleted Id : ' . $roleId);
            DB::commit();
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    public function checkValidation($data)
    {
        $this->validate($data, [
            'role_name' => ['required', 'unique:role,role_name,"' . $data['id'] . '",id,deleted_at,NULL', 'max:100'],
            'user_type_id' => 'required|exists:user_type,id',
            'role_desc' => 'max:255'
        ]);
    }
}

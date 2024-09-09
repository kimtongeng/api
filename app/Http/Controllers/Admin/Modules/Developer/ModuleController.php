<?php

namespace App\Http\Controllers\Admin\Modules\Developer;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use App\Models\RoleModule;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use function response;

class ModuleController extends Controller
{
    /**
     * Get all module data from database
     */
    public function get(Request $request)
    {
        if (UserType::authType(UserType::IDG['ID'])) {
            $tableSize = $request->input('table_size');
            $tableSize = empty($tableSize) ? 10 : $tableSize;

            $filter = $request->input('filter');
            $sortBy = $request->input('sort_by');
            $sortType = $request->input('sort_type');
            
            $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
            $data = Module::lists($filter, $sortBy, $sortType)
            ->paginate($tableSize);
            $response = [
                'pagination' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'from' => intval($data->firstItem()),
                    'to' => intval($data->lastItem()),
                ],
                'data' => $data->items(),
                'success' => 1,
                'message' => 'Success',
            ];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 0, 'message' => 'Permission denied.'], 403);
        }
    }


    public function store(Request $request)
    {
        if (UserType::authType(UserType::IDG['ID'])) {
            $validator = Module::checkValidation($request->all());
            if ($validator->fails()) {
                return response()->json(['error' => 0, 'message' => 'Invalid form', 'errors' => $validator->messages()], 422);
            } else {
                DB::beginTransaction();

                /* Module */
                $module = new Module();
                $module->setData($request);
                $module->save();

                /* Permission */
                foreach ($request['permission'] as $item) {
                    $permission_data = [
                        Permission::PERMISSION_NAME => $item['permission_name'],
                        Permission::MODULE_ID => $module->id,
                    ];

                    $permission = new Permission();
                    $permission->setData($permission_data);
                    $permission->save();
                }
                DB::commit();
                return response()->json(['success' => 1, 'message' => 'Module saved successfully.'], 200);
            }
        } else {
            return response()->json(['error' => 0, 'message' => 'Permission denied.'], 403);
        }
    }

    /**
     * Update Module
     */
    public function update(Request $request)
    {
        if (UserType::authType(UserType::IDG['ID'])) {
            $validator = Module::checkValidation($request->all());
            if ($validator->fails()) {
                return response()->json(['error' => 0, 'message' => 'Invalid form.', 'errors' => $validator->messages()], 422);
            } else {
                DB::beginTransaction();

                /* Update Module */
                $module = Module::find($request->input('id'));
                $module->setData($request);
                $module->save();

                //Delete Permission By Module_id
                // $permission = Permission::where('module_id', $module->id);
                // if ($permission) {
                //     $permission->delete();
                // }

                /*Insert New Permission */
                foreach ($request['permission'] as $item) {
                    $permission_data = [
                        Permission::PERMISSION_NAME => $item['permission_name'],
                        Permission::MODULE_ID => $module->id,
                    ];

                    if (empty($item['id'])) {
                        $permission = new Permission();
                    } else {
                        $permission = Permission::find($item['id']);
                    }

                    $permission->setData($permission_data);
                    $permission->save();
                }

                //Check have delete permission or not
                foreach ($request['deleted_permission'] as $obj) {
                    if (!empty($obj['id'])) {
                        $permission = Permission::find($obj['id']);
                        if ($permission->delete()) {
                            RoleModule::where(RoleModule::MODULE_ID, $module->id)
                                ->where(RoleModule::PERMISSION_ID, $obj['id'])
                                ->delete();
                        }
                    }
                }

                DB::commit();
                return response()->json(['success' => 1, 'message' => 'Module updated successfully.'], 200);
            }
        } else {
            return response()->json(['error' => 0, 'message' => 'Permission denied.'], 403);
        }
    }

    /**
     * Delete Module
     */
    public function delete(Request $request)
    {
        if (UserType::authType(UserType::IDG['ID'])) {
            $module_id = $request->input('id');
            DB::beginTransaction();

            DB::table('role_module')->where('module_id', $module_id)->delete();
            DB::table('permission')->where('module_id', $module_id)->delete();
            DB::table('module')->where('id', $module_id)->delete();

            DB::commit();
            return response()->json(['success' => 1, 'message' => 'Module deleted successfully.'], 200);
        } else {
            return response()->json(['error' => 0, 'message' => 'Permission denied.'], 403);
        }
    }
}

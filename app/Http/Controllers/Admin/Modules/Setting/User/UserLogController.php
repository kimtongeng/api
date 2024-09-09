<?php

namespace App\Http\Controllers\Admin\Modules\Setting\User;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use App\Models\UserLog;
use App\Models\UserType;
use Illuminate\Http\Request;
use function response;

class UserLogController extends Controller
{
    const MODULE_KEY = 'user_log';

    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $tableSize = $request->input('table_size');
            $filter = $request->input('filter');
            $sortBy = $request->input('sort_by');
            $sortType = $request->input('sort_type');

            $data = UserLog::getList($tableSize, $filter, $sortBy, $sortType);
            $module = [];
            if (UserType::isVIPUser()) {
                $module = Module::orderBy('sequence')->select('module_key')->get();
            }
            $response = [
                'pagination' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'from' => intval($data->firstItem()),
                    'to' => intval($data->lastItem())
                ],
                'data' => [
                    'list' => $data->items(),
                    'module' => $module
                ],
                'success' => 1,
                'message' => 'Your action has been completed successfully.'
            ];
            return response()->json($response, 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }
}

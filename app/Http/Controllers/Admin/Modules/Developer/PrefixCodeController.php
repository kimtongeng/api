<?php

namespace App\Http\Controllers\Admin\Modules\Developer;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PrefixCode;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrefixCodeController extends Controller
{
    const MODULE_KEY = 'prefix_code';

    //prefix_code lists
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

    //prefix_code store
    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);
            DB::beginTransaction();

            $prefix_code = new PrefixCode();
            $prefix_code->setData($request);
            if ($prefix_code->save()) {
                // Set Log
                $description = 'Id : ' . $prefix_code->id . ', name : ' . $prefix_code->getName();
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();
            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    //prefix_code update
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $this->checkValidation($request);
            DB::beginTransaction();

            $prefix_code = PrefixCode::find($request['id']);
            if ($prefix_code) {
                $prefix_code->setData($request);
                $prefix_code->save();
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), 'Updated Id : ' . $request['id']);
            }

            DB::commit();
            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    //check validation function
    public function checkValidation($data)
    {
        $this->validate($data, [
            'type' => ['required', 'unique:prefix_code,type,' . $data['id'], 'max:255'],
            'prefix' => ['required', 'unique:prefix_code,prefix,' . $data['id'], 'max:255'],
            'code_length' => 'required|numeric',
        ]);
    }

    //get all list function
    private function getList($tableSize, $filter , $sortBy = '' , $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }
        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = PrefixCode::lists($filter , $sortBy , $sortType)
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
            'combo_list' => PrefixCode::getComboList()
        ];
        return $response;
    }
}

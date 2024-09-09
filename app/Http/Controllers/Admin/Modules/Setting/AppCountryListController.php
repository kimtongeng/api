<?php

namespace App\Http\Controllers\Admin\Modules\Setting;

use App\Models\Permission;
use Laravel\Lumen\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppCountry;

class AppCountryListController extends Controller
{
    const MODULE_KEY = 'app_country';

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
    private function getList($tableSize, $filter = [])
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $data = AppCountry::lists($filter)->paginate($tableSize);

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
}

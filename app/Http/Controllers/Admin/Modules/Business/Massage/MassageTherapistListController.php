<?php

namespace App\Http\Controllers\Admin\Modules\Business\Massage;

use App\Models\Contact;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Enums\Types\ContactStatus;
use App\Enums\Types\IsBusinessOwner;
use App\Http\Controllers\Controller;

class MassageTherapistListController extends Controller
{
    const MODULE_KEY = 'massage_therapist_list';

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

    public function getList($tableSize, $filter = [], $sortBy = '', $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $filter['is_admin_request'] = true;
        // $filter['created_at_range'] = $filter['date_time_picker'];
        $data = Contact::select(
            'id',
            'fullname as name',
            'gender',
            'agency_phone as phone',
            'profile_image',
            'created_at',
        )
        ->where('status', ContactStatus::getActivated())
        ->where('is_massager', IsBusinessOwner::getYes())
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
}

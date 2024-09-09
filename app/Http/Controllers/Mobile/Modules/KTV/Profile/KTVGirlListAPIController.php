<?php

namespace App\Http\Controllers\Mobile\Modules\KTV\Profile;

use Illuminate\Http\Request;
use App\Models\BusinessStaff;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessStaffStatus;

class KTVGirlListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Get KTV Girl Filter & Sort
    public function getKTVGirlFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');

        $data = BusinessStaff::listsKTVGirl($filter)
            ->where('business_staff.status', BusinessStaffStatus::getEnable())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get KTV SHOP For KTV Girl
    public function getKTVListForKTVGirl(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.contact_id' => 'required|exists:contact,id',
        ]);

        $tableSize = $request->input('table_size') ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = BusinessStaff::listsKTVGirl($filter, $sort)
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

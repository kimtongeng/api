<?php

namespace App\Http\Controllers\Mobile\Modules\Massage\Profile;

use App\Enums\Types\BusinessStaffStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BusinessStaff;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\TransactionStatus;

class MassageTherapistListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function getMassageTherapistFilterSort(Request $request)
    {
        $this->validate($request, [
            'book_date' => 'required',
            'business_id' => 'required|exists:business,id',
        ]);

        $bookDate = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->input('book_date'));
        $checkInDate =$bookDate->format('Y-m-d');
        $businessID = $request->input('business_id');

        $data = BusinessStaff::listMassageTherapist()
                ->join('business_staff_workdays', 'business_staff.contact_id', 'business_staff_workdays.contact_id')
                ->where('business_staff_workdays.day', '=', DB::raw("WEEKDAY('" . $checkInDate . "')"))
                ->where('business_staff.business_id',  $businessID)
                ->where('business_staff.status', BusinessStaffStatus::getEnable())
                ->whereNull('business_staff.deleted_at')
                ->groupBy('business_staff.id')
                ->get();

        return $this->responseWithData($data);
    }

    public function getMassageListShopForMassageTherapist(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.contact_id' => 'required|exists:contact,id',
        ]);

        $tableSize = $request->input('table_size') ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = BusinessStaff::listMassageTherapist($filter, $sort)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

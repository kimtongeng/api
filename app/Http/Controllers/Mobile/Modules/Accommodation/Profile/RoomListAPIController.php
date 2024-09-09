<?php

namespace App\Http\Controllers\Mobile\Modules\Accommodation\Profile;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Enums\Types\RoomStatus;
use App\Http\Controllers\Controller;

class RoomListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Room Filter Sort List
     *
     */
    public function getRoomFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Room::lists($filter, $sort)
        ->where('room.status','=', RoomStatus::getEnable())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

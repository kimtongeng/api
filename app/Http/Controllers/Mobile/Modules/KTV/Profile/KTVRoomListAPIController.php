<?php

namespace App\Http\Controllers\Mobile\Modules\KTV\Profile;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Enums\Types\RoomStatus;
use App\Http\Controllers\Controller;

class KTVRoomListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    // KTV Room List
    public function getKTVRoomFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Room::listsKTVRoom($filter, $sort)
        ->where('room.status', RoomStatus::getEnable())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

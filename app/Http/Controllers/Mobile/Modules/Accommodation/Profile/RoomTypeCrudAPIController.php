<?php

namespace App\Http\Controllers\Mobile\Modules\Accommodation\Profile;

use Carbon\Carbon;
use App\Models\RoomType;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RoomTypeCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Validation
     *
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:room_type,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'name' => 'required',
        ]);
    }

    /**
     * Add Room Type
     *
     */
    public function addRoomType(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        //Set Data
        $room_type = new RoomType();
        $room_type->setData($request);
        $room_type->created_at = Carbon::now();

        if($room_type->save()) {
            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Room Type
     *
     */
    public function editRoomType(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        // Find Current Data
        $room_type = RoomType::find($request->input(RoomType::ID));

        if(!empty($room_type)) {
            //Set Data
            $room_type->setData($request);
            $room_type->updated_at = Carbon::now();

            if($room_type->save()) {
                DB::commit();
                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Delete Room Type
     *
     */
    public function deleteRoomType(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:room_type,id',
        ]);

        DB::beginTransaction();

        $room_type = RoomType::find($request->input(RoomType::ID));
        $room_type->delete();

        DB::commit();

        return $this->responseWithSuccess();
    }

    /**
     * Get List Room Type
     *
     */
    public function getRoomTypeList(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
        ]);

        $businessId = $request->input('business_id');
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $data = RoomType::join('business', 'business.id', 'room_type.business_id')
        ->where('business.id', $businessId)
            ->whereNUll('room_type.deleted_at')
            ->select(
                'room_type.id',
                'room_type.business_id',
                'room_type.name',
                'room_type.created_at'
            )
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

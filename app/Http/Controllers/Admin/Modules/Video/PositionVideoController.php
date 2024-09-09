<?php

namespace App\Http\Controllers\Admin\Modules\Video;

use App\Enums\Types\PositionStatus;
use App\Enums\Types\PositionType;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Position;
use App\Models\UserLog;
use App\Models\Video;
use App\Models\VideoPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionVideoController extends Controller
{
    const MODULE_KEY = 'position_video';

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

    private function getList($tableSize, $filter, $sortBy = '', $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $filter['type'] = PositionType::getVideo();
        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = Position::lists($filter , $sortBy , $sortType)
        ->addSelect(
            DB::raw("
                CASE WHEN position.status = '".PositionStatus::getEnable()."'
                THEN 'true'
                ELSE 'false'
                END 'status'
            ")
        )
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

    /**
     * Get Select Data For Form Add, Update
     */
    public function getSelectData(Request $request)
    {
        $data = [];

        return $this->responseWithData($data);
    }

    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $position = new Position();

            $request->merge([Position::TYPE => PositionType::getVideo()]);
            $position->setData($request);

            if ($position->save()) {
                if (!empty($request['video_list'])) {
                    foreach ($request['video_list'] as $item) {
                        $video_position_data = [
                            VideoPosition::VIDEO_ID => $item['id'],
                            VideoPosition::POSITION_ID => $position->id,
                            VideoPosition::ORDER => $item['order'],
                        ];

                        $video_position = new VideoPosition();
                        $video_position->setData($video_position_data);
                        $video_position->save();
                    }
                }

                // Set Log
                $description = 'Id : ' . $position->id;
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }
            DB::commit();

            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function edit(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:position,id'
            ]);

            $filter['position_id'] = $request->input('id');

            $data = Position::lists($filter)
                ->with([
                    'videoList' => function ($query) {
                        $query->select(
                            'video.*',
                            'video.id as id',
                            'video_position.id as video_position_id',
                            'video_position.position_id',
                            DB::raw("'true' as selected")
                        )
                            ->orderBy('video_position.order')
                            ->groupBy('video_position.video_id')
                            ->get();
                    }
                ])->first();

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $position = Position::find($request->input('id'));
            $position->setData($request);

            if ($position->save()) {

                if (!empty($request['video_list'])) {
                    //Insert Or Update
                    foreach ($request['video_list'] as $item) {
                        $video_position_data = [
                            VideoPosition::VIDEO_ID => $item['id'],
                            VideoPosition::POSITION_ID => $position->id,
                            VideoPosition::ORDER => $item['order'],
                        ];

                        if (empty($item['video_position_id'])) {
                            $video_position = new VideoPosition();
                        } else {
                            $video_position = VideoPosition::find($item['video_position_id']);
                        }

                        $video_position->setData($video_position_data);
                        $video_position->save();
                    }

                    //Deleted Video Position
                    foreach ($request['deleted_video_list'] as $obj) {
                        if (!empty($obj['video_position_id'])) {
                            VideoPosition::where(VideoPosition::ID, $obj['video_position_id'])->delete();
                        }
                    }
                }

                $description = 'Id : ' . $position->id;
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }
            DB::commit();
            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }


    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:position,id'
            ]);

            DB::beginTransaction();
            $position = Position::find($request['id']);

            if ($position->delete()) {
                VideoPosition::where(VideoPosition::POSITION_ID, $position->id)->delete();

                $description = 'Id : ' . $position->id;
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }
            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    public function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:position,id' : 'nullable',
            'page' => 'required',
            'name' => 'required',
            'status' => 'required'
        ]);
    }

    public function getVideoList(Request $request)
    {
        $filter = $request->input('filter');
        $data = Video::lists($filter)->get();

        return $this->responseWithData($data);
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:position,id'
        ]);

        DB::beginTransaction();

        $position = Position::find($request->input('id'));
        $position->status = $request->input('status');
        if ($position->save()) {
            $description = ' Id : ' . $position->id . ', Change Status To: ' . $position->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }
}

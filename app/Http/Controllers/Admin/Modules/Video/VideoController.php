<?php

namespace App\Http\Controllers\Admin\Modules\Video;

use App\Enums\Types\VideoStatus;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\UserLog;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    const MODULE_KEY = 'video';

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

        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = Video::lists($filter , $sortBy , $sortType)
        ->addSelect(
            DB::raw("
            CASE WHEN video.status = '" . VideoStatus::getEnable() . "'
            THEN 'true'
            ELSE 'false'
            END status
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

    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $video = new Video();
            $video->setData($request);

            if ($video->save()) {
                // Set Log
                $description = 'Id : ' . $video->id . ', Name : ' . $video->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();

            $data = $this->getList($request->input('table_size'), $request->input('filter'));
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

            $video = Video::find($request['id']);
            $video->setData($request);

            if ($video->save()) {
                $description = 'Id : ' . $video->id . ', Name : ' . $video->name;
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
                'id' => 'required|exists:video,id'
            ]);

            DB::beginTransaction();

            $video = Video::find($request['id']);

            if ($video->delete()) {
                $description = 'Id : ' . $video->id . ', Name : ' . $video->name;
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
        $uniqueName = false;
        $oldVideo = Video::find($data['id']);

        if (!empty($oldVideo)) {
            //When Update
            if ($data['name'] != $oldVideo->name) {
                $uniqueName = true;
            }
        } else {
            //When Add
            $uniqueName = true;
        }

        $messages = [
            'name.unique' => 'validation_unique_name'
        ];

        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:video,id' : 'nullable',
            'name' => $uniqueName ? 'required|unique:video,name,NULL,id,deleted_at,NULL' : 'required',
            'url' => 'required'
        ], $messages);
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:video,id'
        ]);

        DB::beginTransaction();

        $video = Video::find($request->input('id'));
        $video->status = $request->input('status');
        if ($video->save()) {
            $description = ' Id : ' . $video->id . ', Change Status To: ' . $video->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    public function getAutoOrder()
    {
        $lastOrder = 0;

        $data = Video::orderBy('order', 'DESC')
            ->first();
        if (!empty($data)) {
            $lastOrder = $data->order + 1;
        }

        return $this->responseWithData($lastOrder);
    }
}

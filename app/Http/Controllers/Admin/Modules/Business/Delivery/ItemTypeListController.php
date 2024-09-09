<?php

namespace App\Http\Controllers\Admin\Modules\Business\Delivery;

use App\Models\UserLog;
use App\Models\ItemType;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ItemTypeListController extends Controller
{
    const MODULE_KEY = 'item_type';

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
        $data = ItemType::lists($filter, $sortBy, $sortType)
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

            $item_type = new ItemType();
            $item_type->setData($request);

            if ($item_type->save()) {
                if (!empty($request->input('image'))) {
                    //Upload Image
                    $image = StringHelper::uploadImage($request->input('image'), ImagePath::itemType);
                    $item_type->image = $image;
                    $item_type->save();
                }

                $description = 'Id : ' . $item_type->id . ', Name : ' . $item_type->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();

            return $this->responseWithData($item_type);
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

            $item_type = ItemType::find($request->input(ItemType::ID));
            $item_type->setData($request);

            if ($item_type->save()) {
                if (!empty($request->input('image'))) {
                    // Update Image
                    $image = StringHelper::editImage(
                        $request->input('image'),
                        $request->input('old_image'),
                        ImagePath::itemType
                    );
                    $item_type->image = $image;
                    $item_type->save();
                }

                $description = 'Id : ' . $item_type->id . ', Name : ' . $item_type->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }

            DB::commit();

            return $this->responseWithData($item_type);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:item_type,id'
            ]);

            DB::beginTransaction();

            $item_type = ItemType::find($request['id']);

            if ($item_type->delete()) {
                StringHelper::deleteImage($item_type->image, ImagePath::itemType);

                ItemType::where(ItemType::ID, $request->input(ItemType::ID))->delete();

                $description = 'Id : ' . $item_type->id . ', Name : ' . $item_type->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:item_type,id' : 'nullable',
            'name' => 'required',
            'order' => 'nullable',
        ]);
    }

    public function getAutoOrder()
    {
        $lastOrder = 0;

        $data = ItemType::orderBy('order', 'DESC')
        ->first();
        if (!empty($data)) {
            $lastOrder = $data->order + 1;
        }

        return $this->responseWithData($lastOrder);
    }
}

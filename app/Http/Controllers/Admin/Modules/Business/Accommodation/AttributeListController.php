<?php

namespace App\Http\Controllers\Admin\Modules\Business\Accommodation;

use App\Enums\Types\AttributeStatus;
use Carbon\Carbon;
use App\Models\UserLog;
use App\Models\Attribute;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\AttributeGroup;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttributeListController extends Controller
{
    const MODULE_KEY = 'attribute';

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
        $data = Attribute::lists($filter, $sortBy, $sortType)
        ->addSelect(
            DB::raw("
                CASE WHEN attribute.status = '" . AttributeStatus::getEnabled() . "'
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

    /**
     *
     * Add Attribute Data
     */
    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $attribute = new Attribute();
            $attribute->setData($request);
            $attribute->created_at = Carbon::now();

            if ($attribute->save()) {
                if (!empty($request->input('image'))) {
                    //Upload Image
                    $image = StringHelper::uploadImage($request->input('image'), ImagePath::attributeImage);
                    $attribute->image = $image;
                    $attribute->save();
                }
            }

            DB::commit();

            return $this->responseWithData($attribute);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Update Attribute Data
     *
     */
    public function update(Request $request)
    {
        if(Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            //Check Validation
            $this->checkValidation($request);

            DB::beginTransaction();

            $attribute = Attribute::find($request->input('id'));
            $attribute->setData($request);
            $attribute->updated_at = Carbon::now();

            if($attribute->save()) {
                if (!empty($request->input('image'))) {
                    // Update Image
                    $image = StringHelper::editImage(
                        $request->input('image'),
                        $request->input('old_image'),
                        ImagePath::attributeImage
                    );
                    $attribute->image = $image;
                    $attribute->save();
                }
            }

            DB::commit();

            return $this->responseWithData($attribute);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:attribute,id'
            ]);

            DB::beginTransaction();

            $attribute = Attribute::find($request['id']);

            StringHelper::deleteImage($attribute->image, ImagePath::attributeImage);

            if ($attribute->delete()) {

                Attribute::where(Attribute::ID, $attribute->id)->delete();

                $description = 'Id : ' . $attribute->id . ', Name : ' . $attribute->name;

            }
            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }


    /**
     *
     * Check Validation
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:attribute,id' : 'nullable',
            'attribute_group_id' => 'required|exists:attribute_group,id',
            'name' => 'required',
            'status' => 'required',
        ]);
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:attribute,id'
        ]);

        DB::beginTransaction();

        $attribute = Attribute::find($request->input('id'));
        $attribute->status = $request->input('status');
        if ($attribute->save()) {
            $description = ' Id : ' . $attribute->id . ', Change Status To: ' . $attribute->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    public function getSelectData(Request $request)
    {
        $attribute_group = AttributeGroup::select(
            'attribute_group.id',
            'attribute_group.business_type_id',
            'attribute_group.name',
            'attribute_group.key',
        )
        ->get();

        $response = [
            'attribute_group' => $attribute_group,
        ];

        return $this->responseWithData($response);

    }
}

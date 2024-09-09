<?php

namespace App\Http\Controllers\Admin\Modules\Banner;

use App\Enums\Types\BannerImageType;
use App\Enums\Types\BannerStatus;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Business;
use App\Models\Category;
use App\Models\Permission;
use App\Models\PropertyType;
use App\Models\Province;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    const MODULE_KEY = 'banner';

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
        $data = Banner::lists($filter, $sortBy, $sortType)
        ->addSelect(
            DB::raw("
            CASE WHEN banner.status = '". BannerStatus::getEnable() ."'
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

    public function getSelectData(Request $request)
    {
        $categoryInShop = Category::listCategory()->whereNull('category.parent_id')->get();
        $data = [
            'property' => Business::listProperty(['is_admin_request' => true])->get(),
            'property_type' => PropertyType::lists()->get(),
            'attraction' => Business::listAttraction(['is_admin_request' => true])->get(),
            'province' => Province::lists()->get(),
            'shop' => Business::listShop(['is_admin_request' => true])->get(),
            'category_in_shop' => $categoryInShop,
            'category_in_shop_all' => Category::listCategory()->get(),
            'hotel' => Business::listAccommodation(['is_admin_request' => true])->get(),
            'massage' => Business::listMassage(['is_admin_request' => true])->get(),
            'ktv' => Business::listKTVs(['is_admin_request' => true])->get(),
        ];

        return $this->responseWithData($data);
    }

    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $banner = new Banner();
            $banner->setData($request);

            if ($banner->save()) {
                // Upload Image
                if (!empty($request['image'])) {

                    $image = StringHelper::uploadImage($request['image'], ImagePath::bannerImagePath);
                    $banner->image = $image;
                    $banner->save();
                }

                // Set Log
                $description = 'Id : ' . $banner->id . ', Name : ' . $banner->name;
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

            $banner = Banner::find($request['id']);
            $banner->setData($request);

            if ($banner->save()) {
                $image = StringHelper::editImage($request['image'], $request['old_image'], ImagePath::bannerImagePath);
                $banner->image = $image;
                $banner->save();

                $description = 'Id : ' . $banner->id . ', Name : ' . $banner->name;
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
                'id' => 'required|exists:banner,id'
            ]);

            DB::beginTransaction();

            $banner = Banner::find($request['id']);

            StringHelper::deleteImage($banner->image, ImagePath::bannerImagePath);

            if ($banner->delete()) {
                $description = 'Id : ' . $banner->id . ', Name : ' . $banner->name;
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
        $oldBanner = Banner::find($data['id']);

        if (!empty($oldBanner)) {
            //When Update
            if ($data['name'] != $oldBanner->name) {
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
            'id' => !empty($data['id']) ? 'required|exists:banner,id' : 'nullable',
            'name' => $uniqueName ? 'required|unique:banner,name,NULL,id,deleted_at,NULL' : 'required',
            'type' => 'required',
            'description' => 'nullable'
        ], $messages);
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:banner,id'
        ]);

        DB::beginTransaction();

        $banner = Banner::find($request->input('id'));
        $banner->status = $request->input('status');
        if ($banner->save()) {
            $description = ' Id : ' . $banner->id . ', Change Status To: ' . $banner->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    public function uploadMedia(Request $request)
    {
        $this->validate($request, [
            'image' => 'required'
        ]);
        $image = StringHelper::uploadImage($request->input('image'), ImagePath::mediaImagePath);

        return $this->responseWithData($image);
    }

    /**
     * Get Category IN Shop
     *
     */
    public function getCategoryInShop(Request $request)
    {
        $id = $request->input('id');
        $data = Category::listCategory()->where('business_id',$id)
        ->whereNull('parent_id')
        ->get();

        return $data;
    }

    /**
     * Get Sub Category In Shop
     *
     */
    public function getSubCategory(Request $request)
    {
        $id = $request['id'];
        $data = Category::where('parent_id',$id)
        ->select('id','name','parent_id')
        ->get();
        return $data;
    }
}

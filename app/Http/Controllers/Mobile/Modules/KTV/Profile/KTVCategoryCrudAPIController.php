<?php

namespace App\Http\Controllers\Mobile\Modules\KTV\Profile;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KTVCategoryCrudAPIController extends Controller
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
        $uniqueName = false;
        $oldCategory = Category::find($data['id']);
        if (!empty($oldCategory)) {
            //When Update
            if ($data['name'] != $oldCategory->name) {
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
            'id' => !empty($data['id']) ? 'required|exists:category,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'name' => $uniqueName ? 'required|unique:category,name,NULL,id,business_id,' . $data['business_id'] : 'required',
        ], $messages);
    }

    /**
     * Add KTV Category
     */
    public function addKTVCategory(Request $request)
    {
        $this->checkValidation($request);

        // Set Data
        $category = new Category();
        $category->setData($request);
        $category->created_at = Carbon::now();

        //Save Data
        if ($category->save()) {
            // Upload Image
            if (!empty($request->input(Category::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(Category::IMAGE), ImagePath::ktvProductCategory);
                $category->{Category::IMAGE} = $image;
                $category->save();
            }
            return $this->responseWithData($category);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit KTV Category
     */
    public function editKTVCategory(Request $request)
    {
        $this->checkValidation($request);

        $category = Category::find($request->input(Category::ID));

        if (!empty($category)) {

            // set Data
            $category->setData($request);
            $category->updated_at = Carbon::now();

            // save Data
            if ($category->save()) {
                if (!empty($request->input(Category::IMAGE))) {
                    // Update Image
                    $image = StringHelper::editImage(
                        $request->input(Category::IMAGE),
                        $request->input('old_image'),
                        ImagePath::ktvProductCategory
                    );
                    $category->{Category::IMAGE} = $image;
                    $category->save();
                }

                return $this->responseWithData($category);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Delete KTV Category
     */
    public function deleteKTVCategory(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:category,id',
        ]);

        DB::beginTransaction();

        $category = Category::find($request->input(Category::ID));

        // Delete Image
        StringHelper::deleteImage($category->{Category::IMAGE}, ImagePath::ktvProductCategory);

        $category->delete();

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get My KTV Category
     */
    public function getMyKTVCategory(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');

        $data = Category::listCategory($filter)->whereNull('parent_id')
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

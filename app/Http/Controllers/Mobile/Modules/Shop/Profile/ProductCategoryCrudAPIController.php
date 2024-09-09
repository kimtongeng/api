<?php

namespace App\Http\Controllers\Mobile\Modules\Shop\Profile;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ProductCategoryCrudAPIController extends Controller
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
     * Add Product Category
     *
     */
    public function addProductCategory(Request $request)
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
                $image = StringHelper::uploadImage($request->input(Category::IMAGE), ImagePath::shopProductCategory);
                $category->{Category::IMAGE} = $image;
                $category->save();
            }
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Product Category
     *
     */
    public function editProductCategory(Request $request)
    {
        $this->checkValidation($request);

        $category = Category::find($request->input(Category::ID));

        if (!empty($category)) {

            // set Data
            $category->setData($request);
            $category->updated_at = Carbon::now();

            // save Data
            if ($category->save()) {
                if(!empty($request->input(Category::IMAGE))){
                    // Update Image
                    $image = StringHelper::editImage(
                        $request->input(Category::IMAGE),
                        $request->input('old_image'),
                        ImagePath::shopProductCategory
                    );
                    $category->{Category::IMAGE} = $image;
                    $category->save();
                }

                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Delete Product Category
     *
     */
    public function deleteProductCategory(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:category,id',
        ]);

        DB::beginTransaction();

        $category = Category::find($request->input(Category::ID));

        // Delete Image
        StringHelper::deleteImage($category->{Category::IMAGE}, ImagePath::shopProductCategory);

        $category->delete();

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get Product Category
     *
     */
    public function getMyProductCategory(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        // $filter = ['business_id' => $request->input('business_id')];
        $filter = $request->input('filter');

        $data = Category::listCategory($filter)->whereNull('parent_id')
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

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

class ProductSubCategoryCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Validation
     *
     */
    public function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:category,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'name' => 'required',
            'parent_id' => 'required|exists:category,id'
        ]);
    }

    /**
     * Add Product Sub Category
     *
     */
    public function addProductSubCategory(Request $request)
    {
        $this->checkValidation($request);

        // Set Data
        $subCategory = new Category();
        $subCategory->setData($request);
        $subCategory->created_at = Carbon::now();

        // Save Data
        if($subCategory->save()){
            // Upload Image
            if(!empty($request->input(Category::IMAGE))){
                $image = StringHelper::uploadImage($request->input(Category::IMAGE), ImagePath::shopProductSubCategory);
                $subCategory->{Category::IMAGE} = $image;
                $subCategory->save();
            }
            return $this->responseWithSuccess();
        }else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

    }

    /**
     *  Edit Product Sub Category
     *
     */
    public function editProductSubCategory(Request $request)
    {
        $this->checkValidation($request);


        $subCategory = Category::find($request->input(Category::ID));

        if(!empty($subCategory)){

            // Set Data
            $subCategory->setData($request);
            $subCategory->updated_at = Carbon::now();

            // Save Data
            if ($subCategory->save()) {
                if(!empty($request->input(Category::IMAGE))){
                    // Update Image
                    $image = StringHelper::editImage(
                        $request->input(Category::IMAGE),
                        $request->input('old_image'),
                        ImagePath::shopProductSubCategory
                    );
                    $subCategory->{Category::IMAGE} = $image;
                    $subCategory->save();
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
    public function deleteProductSubCategory(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:category,id',
        ]);

        DB::beginTransaction();

        $subCategory = Category::find($request->input(Category::ID));

        // Delete Image
        StringHelper::deleteImage($subCategory->{Category::IMAGE}, ImagePath::shopProductSubCategory);

        $subCategory->delete();

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get Product Category
     *
     */
    public function getMyProductSubCategory(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
            'filter.parent_id' => 'required|exists:category,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        // $filter = ['business_id' => $request->input('business_id')];
        $filter = $request->input('filter');

        $data = Category::listCategory($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

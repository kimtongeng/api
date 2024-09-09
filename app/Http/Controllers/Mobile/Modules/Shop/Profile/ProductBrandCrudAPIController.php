<?php

namespace App\Http\Controllers\Mobile\Modules\Shop\Profile;

use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductBrandCrudAPIController extends Controller
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
        $uniqueName = false;
        $oldBrand = Brand::find($data['id']);
        if (!empty($oldBrand)) {
            //When Update
            if ($data['name'] != $oldBrand->name) {
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
            'id' => !empty($data['id']) ? 'required|exists:brand,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'name' => $uniqueName ? 'required|unique:brand,name,NULL,id,business_id,' . $data['business_id'] : 'required',
        ], $messages);
    }

    /**
     * Add Product Brand
     *
     */
    public function addProductBrand(Request $request)
    {
        $this->checkValidation($request);

        //Set Data
        $brand = new Brand();
        $brand->setData($request);
        $brand->created_at = Carbon::now();

        // Save Data
        if ($brand->save()) {
            // Upload Image
            if (!empty($request->input(Brand::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(Brand::IMAGE), ImagePath::shopProductBrand);
                $brand->{Brand::IMAGE} = $image;
                $brand->save();
            }
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Product Brand
     *
     */
    public function editProductBrand(Request $request)
    {
        $this->checkValidation($request);

        $brand = Brand::find($request->input(Brand::ID));

        if (!empty($brand)) {
            // Set Data
            $brand->setData($request);
            $brand->updated_at = Carbon::now();

            // Save Data
            if ($brand->save()) {
                if(!empty($request->input(Brand::IMAGE))){
                    $image = StringHelper::editImage(
                        $request->input(Brand::IMAGE),
                        $request->input('old_image'),
                        ImagePath::shopProductBrand
                    );
                    $brand->{Brand::IMAGE} = $image;
                    $brand->save();
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
     * Delete Product Brand
     *
     */
    public function deleteProductBrand(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:brand,id'
        ]);

        DB::beginTransaction();

        $brand = Brand::find($request->input(Brand::ID));

        //Delete Image
        StringHelper::deleteImage($request->input(Brand::IMAGE), ImagePath::shopProductBrand);

        $brand->delete();

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get List Product Brand
     *
     */
    public function getMyProductBrand(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required:exists:business,id',
        ]);

        $tableSize = !empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');

        $data = Brand::listBrand($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

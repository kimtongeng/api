<?php

namespace App\Http\Controllers\Mobile\Modules\Shop\Profile;

use App\Models\Models;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ProductModelCrudAPIController extends Controller
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
            'id' => !empty($data['id']) ? 'required|exists:model,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'name' => 'required',
        ]);
    }

    /**
     * Add Product Brand
     *
     */
    public function addProductModel(Request $request)
    {
        $this->checkValidation($request);

        //Set Data
        $model = new Models();
        $model->setData($request);
        $model->created_at = Carbon::now();

        // Save Data
        if ($model->save()) {
            // Upload Image
            if (!empty($request->input(Models::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(Models::IMAGE), ImagePath::shopProductModel);
                $model->{Models::IMAGE} = $image;
                $model->save();
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
    public function editProductModel(Request $request)
    {
        $this->checkValidation($request);

        $model = Models::find($request->input(Models::ID));

        if (!empty($model)) {
            // Set Data
            $model->setData($request);
            $model->updated_at = Carbon::now();

            // Save Data
            if ($model->save()) {
                if(!empty($request->input(Models::IMAGE))){
                    $image = StringHelper::editImage(
                        $request->input(Models::IMAGE),
                        $request->input('old_image'),
                        ImagePath::shopProductModel
                    );
                    $model->{Models::IMAGE} = $image;
                    $model->save();
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
    public function deleteProductModel(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:model,id'
        ]);

        DB::beginTransaction();

        $model = Models::find($request->input(Models::ID));

        //Delete Image
        StringHelper::deleteImage($request->input(Models::IMAGE), ImagePath::shopProductModel);

        $model->delete();

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get List Product Brand
     *
     */
    public function getMyProductModel(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required:exists:business,id',
        ]);

        $tableSize = !empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');

        $data = Models::listModel($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

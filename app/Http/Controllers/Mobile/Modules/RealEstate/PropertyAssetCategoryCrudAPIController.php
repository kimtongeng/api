<?php


namespace App\Http\Controllers\Mobile\Modules\RealEstate;


use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PropertyAssetCategoryCrudAPIController extends Controller
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
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:asset_category,id' : 'nullable',
            'property_id' => 'required|exists:business,id',
            'name' => 'required'
        ]);
    }

    //Get Asset Category List
    public function getAssetCategoryList(Request $request)
    {
        $filter = [
            'search' => $request->input('search'),
            'property_id' => $request->input('property_id')
        ];
        $data = AssetCategory::lists($filter)->get();

        return $this->responseWithData($data);
    }

    /**
     * Add Asset Category
     *
     */
    public function addAssetCategory(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        $request->merge([AssetCategory::BUSINESS_ID => $request->input('property_id')]);

        //Set Data
        $asset_category = new AssetCategory();
        $asset_category->setData($request);
        $asset_category->{AssetCategory::CREATED_AT} = Carbon::now();
        if ($asset_category->save()) {
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Asset Category
     *
     */
    public function editAssetCategory(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        $request->merge([AssetCategory::BUSINESS_ID => $request->input('property_id')]);

        //Set Data
        $asset_category = AssetCategory::find($request->input(AssetCategory::ID));
        $asset_category->setData($request);
        $asset_category->{AssetCategory::UPDATED_AT} = Carbon::now();
        if ($asset_category->save()) {
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Delete Asset Category
     *
     */
    public function deleteAssetCategory(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'id' => 'required|exists:asset_category,id',
        ]);

        //Delete Data
        $asset_category = AssetCategory::find($request->input(AssetCategory::ID));
        if ($asset_category->delete()) {
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

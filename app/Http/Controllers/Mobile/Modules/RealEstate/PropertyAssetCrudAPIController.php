<?php


namespace App\Http\Controllers\Mobile\Modules\RealEstate;

use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\PropertyAssetActive;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\GalleryPhoto;
use App\Models\PropertyAsset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyAssetCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile,check_user_is_property_owner');
    }

    /**
     * Check Validation
     *
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:property_asset,id' : 'nullable',
            'current_user_id' => 'required|exists:contact,id',
            'property_id' => 'required|exists:business,id',
            'asset_category_id' => 'required|exists:asset_category,id',
            'code' => 'required',
            'description' => 'required',
            'size' => 'required',
            'price' => 'required',
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            'gallery_photo' => 'nullable',
            'deleted_gallery_photo' => !empty($data['id']) ? 'nullable' : 'nullable',
        ]);
    }

    /**
     * Add Asset
     *
     */
    public function addAsset(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([PropertyAsset::BUSINESS_ID => $request->input('property_id')]);

        //Set Data
        $property_asset = new PropertyAsset();
        $property_asset->setData($request);
        $property_asset->{PropertyAsset::CREATED_AT} = Carbon::now();

        //Save Data
        if ($property_asset->save()) {
            //Upload Thumbnail
            if (!empty($request->input(PropertyAsset::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(PropertyAsset::IMAGE), ImagePath::propertyThumb);
                $property_asset->{PropertyAsset::IMAGE} = $image;
                $property_asset->save();
            }

            //Upload Gallery Photo
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getPropertyAsset(),
                        GalleryPhoto::TYPE_ID => $property_asset->{PropertyAsset::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::propertyGallery);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                    }
                }
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Asset
     *
     */
    public function editAsset(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([PropertyAsset::BUSINESS_ID => $request->input('property_id')]);

        //Find Current Data
        $property_asset = PropertyAsset::find($request->input(PropertyAsset::ID));

        //Find Asset Category Data
        $oldAssetCategoryID = $property_asset->{PropertyAsset::ASSET_CATEGORY_ID};
        $assetCategoryData = AssetCategory::find($oldAssetCategoryID);
        $newAssetCategoryID = $request->input(PropertyAsset::ASSET_CATEGORY_ID);

        //Set New Data
        $property_asset->setData($request);
        $property_asset->{PropertyAsset::UPDATED_AT} = Carbon::now();

        //Save Data
        if ($property_asset->save()) {
            //Upload Thumbnail
            $image = StringHelper::editImage(
                $request->input(PropertyAsset::IMAGE),
                $request->input('old_' . PropertyAsset::IMAGE),
                ImagePath::propertyThumb
            );
            $property_asset->{PropertyAsset::IMAGE} = $image;
            $property_asset->save();

            //Upload Or Update Gallery Photo
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $orderNumber = $key + 1;
                    if (empty($obj[GalleryPhoto::ID])) {
                        $data = [
                            GalleryPhoto::TYPE => GalleryPhotoType::getPropertyAsset(),
                            GalleryPhoto::TYPE_ID => $property_asset->{PropertyAsset::ID},
                            GalleryPhoto::ORDER => $orderNumber
                        ];

                        $gallery_photo = new GalleryPhoto();

                        $gallery_photo->setData($data);
                        if ($gallery_photo->save()) {
                            //Upload Image
                            $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::propertyGallery);
                            $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                            $gallery_photo->save();
                        }
                    } else {
                        $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                        $gallery_photo->{GalleryPhoto::ORDER} = $orderNumber;
                        $gallery_photo->save();
                    }
                }
            }

            //Check have deleted Gallery Photo
            if (!empty($request->input('deleted_gallery_photo'))) {
                foreach ($request['deleted_gallery_photo'] as $obj) {
                    if (!empty($obj[GalleryPhoto::ID])) {
                        $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                        $gallery_photo->delete();
                        StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::propertyGallery);
                    }
                }
            }

            //Change to new asset category if old asset category has deleted
            if (empty($assetCategoryData)) {
                DB::table(PropertyAsset::TABLE_NAME)
                    ->where(PropertyAsset::ASSET_CATEGORY_ID, $oldAssetCategoryID)
                    ->update([
                        PropertyAsset::ASSET_CATEGORY_ID => $newAssetCategoryID,
                        PropertyAsset::UPDATED_AT => Carbon::now()
                    ]);
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Delete Asset
     *
     */
    public function deleteAsset(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'id' => 'required|exists:property_asset,id',
            'property_id' => 'required|exists:business,id',
        ]);

        DB::beginTransaction();

        $property_asset = PropertyAsset::find($request->input(PropertyAsset::ID));

        //Delete Thumbnail
        StringHelper::deleteImage($property_asset->{PropertyAsset::IMAGE}, ImagePath::propertyThumb);

        //Delete Gallery Photo
        $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getPropertyAsset())
            ->where(GalleryPhoto::TYPE_ID, $request->input('property_id'))
            ->get();
        foreach ($gallery_photo_list as $obj) {
            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::propertyGallery);
            $gallery_photo->delete();
        }

        if ($property_asset->delete()) {
            //Delete Asset Category
            AssetCategory::where(AssetCategory::ID, $property_asset->{PropertyAsset::ASSET_CATEGORY_ID})->delete();
        }

        DB::commit();

        return $this->responseWithSuccess();
    }

    /**
     * Get Asset List
     *
     */
    public function getAssetList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.property_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 3 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = PropertyAsset::lists($filter, $sort)
            ->where('property_asset.active', PropertyAssetActive::getTrue())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

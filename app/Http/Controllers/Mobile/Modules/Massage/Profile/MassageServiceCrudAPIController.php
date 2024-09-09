<?php

namespace App\Http\Controllers\Mobile\Modules\Massage\Profile;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Business;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Enums\Types\IsDiscount;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\GalleryPhotoType;
use App\Helpers\Utils\ErrorCode;

class MassageServiceCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Check Validation
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:product,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'price' => 'required',
            'status' => 'required',
            'description' => 'required',
            'duration' => 'required',
            'type' => 'required',
        ]);
    }

    /**
     * Add Massage Service
     *
     */
    public function addMassageService(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        //Merge Value Some Request
        $request->merge([
            Product::COUNTRY_ID => Business::find($request->input('business_id'))->{Business::COUNTRY_ID},
        ]);

        //Set Data
        $massage = new Product();
        $massage->setData($request);
        $massage->created_at = Carbon::now();

        //Set Data
        if ($massage->save()) {
            // Upload Image
            if (!empty($request->input('image'))) {
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::massageServiceThumbnail);
                $massage->{Product::IMAGE} = $image;
                $massage->save();
            }

            //Upload Gallery
            if (!empty($request->input('gallery_photo'))) {
                foreach($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getMassagerService(),
                        GalleryPhoto::TYPE_ID => $massage->id,
                        GalleryPhoto::ORDER => $key + 1
                    ];
                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if($gallery_photo->save()) {
                        // Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::massageServiceGallery);
                        $gallery_photo->image = $image;
                        $gallery_photo->save();
                    }
                }
            }

            //Set Discount
            $isDiscount = IsDiscount::getNo();
            $discountAmount = floatval($request->input('discount_amount'));
            $discountType = 0;
            if (!empty($discountAmount)) {
                if (
                    $discountAmount > 0
                ) {
                    $isDiscount = IsDiscount::getYes();
                    $discountType = $request->input('discount_type');
                }
            }

            $massage->is_discount = $isDiscount;
            $massage->discount_amount = $discountAmount;
            $massage->discount_type = $discountType;
            $grand_total = $massage->getSellPrice();
            $massage->sell_price = $grand_total;
            $massage->save();

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Massage Service
     *
     */
    public function editMassageService(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $massage = Product::find($request->input(Product::ID));

        if(!empty($massage)) {
            //Set Data
            $massage->setData($request);
            $massage->updated_at = Carbon::now();

            //Save Data
            if($massage->save()) {
                //Update Image
                if(!empty($request->input('image'))) {
                    $image = StringHelper::editImage(
                        $request->input(Product::IMAGE),
                        $request->input('old_image'),
                        ImagePath::massageServiceThumbnail
                    );
                    $massage->image = $image;
                    $massage->save();
                }

                // Update or Upload Image
                if(!empty($request->input('gallery_photo'))) {
                    foreach($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if(empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getMassagerService(),
                                GalleryPhoto::TYPE_ID => $massage->{Product::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo = new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                // Upload Image
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::massageServiceGallery);
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
                if(!empty($request->input('deleted_gallery_photo'))) {
                    foreach($request->input('deleted_gallery_photo') as $obj) {
                        if(!empty($obj[GalleryPhoto::ID])) {
                            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                            $gallery_photo->delete();
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::massageServiceGallery);
                        }
                    }
                }

                //Set Discount
                $isDiscount = IsDiscount::getNo();
                $discountAmount = floatval($request->input('discount_amount'));
                $discountType = 0;
                if (!empty($discountAmount)) {
                    if (
                        $discountAmount > 0
                    ) {
                        $isDiscount = IsDiscount::getYes();
                        $discountType = $request->input('discount_type');
                    }
                }

                $massage->is_discount = $isDiscount;
                $massage->discount_amount = $discountAmount;
                $massage->discount_type = $discountType;
                $grand_total = $massage->getSellPrice();
                $massage->sell_price = $grand_total;
                $massage->save();

                DB::commit();

                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Delete Massage Service
     *
     */
    public function deleteMassageService(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:product,id'
        ]);

        DB::beginTransaction();

        $massage = Product::find($request->input('id'));

        if($massage->delete()) {
            //Delete Thumbnail
            StringHelper::deleteImage($massage->{Product::IMAGE}, ImagePath::massageServiceThumbnail);

            //Delete Gallery Photo Single or Multi
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getMassagerService())
                ->where(GalleryPhoto::TYPE_ID, $massage->{Product::ID})
                ->get();
            foreach($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::massageServiceGallery);
                $gallery_photo->delete();
            }
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get Massage Service
     *
     */
    public function getMassageService(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Product::listMassageService($filter, $sort)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

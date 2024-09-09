<?php

namespace App\Http\Controllers\Mobile\Modules\KTV\Profile;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Enums\Types\IsDiscount;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\GalleryPhotoType;
use App\Helpers\Utils\ErrorCode;

class KTVRoomCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Check Validation
    private function checkValidation($data)
    {
        $uniqueCode = false;
        $oldKTVRoom = Room::find($data['id']);

        if (!empty($oldKTVRoom)) {
            //When Update
            if ($data['code'] != $oldKTVRoom->code) {
                $uniqueCode = true;
            }
        } else {
            //When Add
            $uniqueCode = true;
        }

        $messages = [
            'code.unique' => 'validation_unique_code'
        ];

        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:room,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'code' => $uniqueCode ? 'required|unique:room,code,NULL,id,business_id,' . $data['business_id'] : 'required',
            'price' => 'required',
            'description' => 'nullable',
            'status' => 'required',
        ], $messages);
    }

    // Add KTV Room
    public function addKTVRoom(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $room = new Room();
        $room->setData($request);
        $room->created_at = Carbon::now();

        //Save Data
        if ($room->save()) {
            //Upload Image
            if (!empty($request->input('image'))) {
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::ktvRoomThumb);
                $room->{Room::IMAGE} = $image;
                $room->save();
            }

            //Upload Gallery
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getKtvRoomCover(),
                        GalleryPhoto::TYPE_ID => $room->id,
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::ktvRoomGallery);
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
            $room->is_discount = $isDiscount;
            $room->discount_amount = $discountAmount;
            $room->discount_type = $discountType;
            $grand_total = $room->getSellPrice();
            $room->total_price = $grand_total;
            $room->save();

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Edit KTV Room
    public function editKTVRoom(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $room = Room::find($request->input('id'));

        if (!empty($room)) {
            //Set Data
            $room->setData($request);
            $room->updated_at = Carbon::now();

            if ($room->save()) {
                //Update Image
                if (!empty($request->input('image'))) {
                    $image = StringHelper::editImage(
                        $request->input('image'),
                        $request->input('old_image'),
                        ImagePath::ktvRoomThumb
                    );
                    $room->{Room::IMAGE} = $image;
                    $room->save();
                }

                //Upload Or Update Gallery
                if (!empty($request->input('gallery_photo'))) {
                    foreach ($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getKtvRoomCover(),
                                GalleryPhoto::TYPE_ID => $room->{Room::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo = new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Image
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::ktvRoomGallery);
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
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::ktvRoomGallery);
                        }
                    }
                }

                //Set Discount
                $isDiscount = IsDiscount::getNo();
                $discountAmount = floatval($request->input('discount_amount'));
                $discountType = 0;
                if (!empty($discountAmount)) {
                    if ($discountAmount > 0) {
                        $isDiscount = IsDiscount::getYes();
                        $discountType = $request->input('discount_type');
                    }
                }
                $room->is_discount = $isDiscount;
                $room->discount_amount = $discountAmount;
                $room->discount_type = $discountType;
                $grand_total = $room->getSellPrice();
                $room->total_price = $grand_total;
                $room->save();

                DB::commit();

                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Delete KTV Room
    public function deleteKTVRoom(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:room,id',
        ]);

        DB::beginTransaction();

        $room = Room::find($request->input('id'));

        if ($room->delete()) {
            //Delete Thumbnail
            StringHelper::deleteImage($room->{Room::IMAGE}, ImagePath::ktvRoomThumb);

            //Delete Gallery Photo Single and Multi
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getKtvRoomCover())
                ->where(GalleryPhoto::TYPE_ID, $room->{Room::ID})
                ->get();
            foreach ($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::ktvRoomGallery);
                $gallery_photo->delete();
            }

            DB::commit();

            return $this->responseWithSuccess();
        }
    }

    // Get My KTV Room
    public function getMyKTVRoom(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Room::listsKTVRoom($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    // Change Status Room
    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:room,id',
            'status' => 'required'
        ]);

        DB::beginTransaction();

        $room = Room::find($request->input('id'));
        $room->status = $request->input('status');
        $room->save();

        DB::commit();
        return $this->responseWithSuccess();
    }
}

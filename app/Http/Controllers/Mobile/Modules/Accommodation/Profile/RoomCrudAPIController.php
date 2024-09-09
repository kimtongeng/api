<?php

namespace App\Http\Controllers\Mobile\Modules\Accommodation\Profile;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\UserLog;
use App\Models\Permission;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Enums\Types\IsDiscount;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\GalleryPhotoType;

class RoomCrudAPIController extends Controller
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
            'price' => 'required',
            'room_type_id' => 'required',
            'description' => 'nullable',
            'status' => 'required',
        ]);
    }

    /**
     *
     * Add Room
     */
    public function addRoom(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $room = new Room();
        $room->setData($request);
        $room->created_at = Carbon::now();

        // Save Data
        if ($room->save()){
            //Upload Image
            if (!empty($request->input('image'))){
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::accommodationRoomThumb);
                $room->{Room::IMAGE} = $image;
                $room->save();
            }

            //Upload Gallery
            if (!empty($request->input('gallery_photo'))){
                foreach($request->input('gallery_photo') as $key => $obj){
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getAccommodationRoom(),
                        GalleryPhoto::TYPE_ID => $room->id,
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::accommodationRoomGallery);
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
                if ($discountAmount > 0
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

    /**
     * Edit Room
     *
     */
    public function editRoom(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $room = Room::find($request->input('id'));


        if (!empty($room)) {
            //Set Data
            $room->setData($request);
            $room->updated_at = Carbon::now();

            //Save Data
            if ($room->save()){
                //Update Image
                if (!empty($request->input('image'))) {
                    $image = StringHelper::editImage($request->input('image'),
                    $request->input('old_image'),
                    ImagePath::accommodationRoomThumb
                    );
                    $room->{Room::IMAGE} = $image;
                    $room->save();
                }

                //Upload Or Update Gallery
                if(!empty($request->input('gallery_photo'))) {
                    foreach($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getAccommodationRoom(),
                                GalleryPhoto::TYPE_ID => $room->{Room::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo = new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Image
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::accommodationRoomGallery);
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
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::accommodationRoomGallery);
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

    /**
     * Delete Room
     *
     */
    public function deleteRoom(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:room,id',
        ]);

        DB::beginTransaction();

        $room = Room::find($request->input('id'));

        if ($room->delete()) {
            //Delete Thumbnail
            StringHelper::deleteImage($room->{Room::IMAGE}, ImagePath::accommodationRoomThumb);

            //Delete Gallery Photo Single and Multi
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getAccommodationRoom())
            ->where(GalleryPhoto::TYPE_ID, $room->{Room::ID})
            ->get();
            foreach ($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::accommodationRoomGallery);
                $gallery_photo->delete();
            }

            DB::commit();

            return $this->responseWithSuccess();
        }
    }

    /**
     * Get My Room
     *
     */
    public function getMyRoom(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Room::lists($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     *Change Status Room
     *
     */
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

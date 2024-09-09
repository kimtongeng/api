<?php

namespace App\Models;

use Mpdf\Tag\Select;
use App\Enums\Types\DiscountType;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\RoomStatus;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    const TABLE_NAME = 'room';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const ROOM_TYPE_ID = 'room_type_id';
    const CODE = 'code';
    const NAME = 'name';
    const IMAGE = 'image';
    const PRICE = 'price';
    const IS_DISCOUNT = 'is_discount';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_TYPE = 'discount_type';
    const TOTAL_PRICE = 'total_price';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    /**
     * RelationShip Area
     *
     */
    //Gallery Photo Relationship
    public function galleryPhoto()
    {
        return $this->hasMany(GalleryPhoto::class, GalleryPhoto::TYPE_ID, GalleryPhoto::ID);
    }


    /**
     * Function Area
     *
     */
    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::NAME} = $data[self::NAME];
        // $this->{self::ROOM_TYPE_ID} = $data[self::ROOM_TYPE_ID];
        isset($data[self::ROOM_TYPE_ID]) && $this->{self::ROOM_TYPE_ID} = $data[self::ROOM_TYPE_ID];
        isset($data[self::CODE]) && $this->{self::CODE} = $data[self::CODE];
        $this->{self::PRICE} = $data[self::PRICE];
        isset($data[self::IS_DISCOUNT]) && $this->{self::IS_DISCOUNT} = $data[self::IS_DISCOUNT];
        isset($data[self::DISCOUNT_AMOUNT]) && $this->{self::DISCOUNT_AMOUNT} = $data[self::DISCOUNT_AMOUNT];
        isset($data[self::DISCOUNT_TYPE]) && $this->{self::DISCOUNT_TYPE} = $data[self::DISCOUNT_TYPE];
        isset($data[self::TOTAL_PRICE]) && $this->{self::TOTAL_PRICE} = $data[self::TOTAL_PRICE];
        isset($data[self::DESCRIPTION]) && $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    //Get Sell Price with discount or not
    public function getSellPrice()
    {
        $sellPrice = $this->{self::PRICE};
        if ($this->{self::DISCOUNT_TYPE} == DiscountType::getAmount()) {
            $sellPrice -= $this->{self::DISCOUNT_AMOUNT};
        } elseif ($this->{self::DISCOUNT_TYPE} == DiscountType::getPercentage()) {
            $sellPrice -= ($this->{self::PRICE} * $this->{self::DISCOUNT_AMOUNT} / 100);
        }
        return $sellPrice;
    }

    //List Room
    public static function lists($filter = [], $sortBy = "", $sortType="DESC")
    {
        //Filter
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $isDiscount = isset($filter['is_discount']) ? $filter['is_discount'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;

        return self::select(
            'room.id',
            'room.name',
            'business.id as business_id',
            'room_type.id as room_type_id',
            'room_type.name as room_type_name',
            'room.image',
            'room.price',
            'room.discount_amount',
            'room.discount_type',
            'room.total_price',
            'room.description',
            'room.status',
            'room.created_at',
        )
        ->join('business', 'business.id', 'room.business_id')
        ->join('room_type','room_type.id','room.room_type_id')
        ->when($businessID, function ($query) use ($businessID) {
            $query->where('room.business_id', $businessID);
        })
        ->when($isDiscount, function ($query) use ($isDiscount) {
            $query->where('room.is_discount', $isDiscount);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('room.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($newest, function ($query) {
            $query->orderBy("room.id", "DESC");
        })
        ->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getAccommodationRoom())
                ->orderBy('gallery_photo.order', 'ASC');
            }
        ]);
    }

    //List KTV Room
    public static function listsKTVRoom($filter = [], $sortBy = "", $sortType="DESC")
    {
        //Filter
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $isDiscount = isset($filter['is_discount']) ? $filter['is_discount'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;

        return self::join('business', 'business.id', 'room.business_id')
        ->when($businessID, function ($query) use ($businessID) {
            $query->where('room.business_id', $businessID);
        })
        ->when($isDiscount, function ($query) use ($isDiscount) {
            $query->where('room.is_discount', $isDiscount);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('room.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($newest, function ($query) {
            $query->orderBy("room.id", "DESC");
        })
        ->select(
            'room.id',
            'room.name',
            'business.id as business_id',
            'room.code',
            'room.image',
            'room.price',
            'room.discount_amount',
            'room.discount_type',
            'room.total_price',
            'room.description',
            'room.status',
            'room.created_at',
        )
        ->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getKtvRoomCover())
                    ->orderBy('gallery_photo.order', 'ASC');
            }
        ]);
    }
}

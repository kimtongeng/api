<?php

namespace App\Models;

use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\IsResizeImage;
use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class PropertyAsset extends Model
{

    use SoftDeletes;

    const TABLE_NAME = 'property_asset';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const ASSET_CATEGORY_ID = 'asset_category_id';
    const IMAGE = 'image';
    const CODE = 'code';
    const SIZE = 'size';
    const PRICE = 'price';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const ACTIVE = 'active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::ASSET_CATEGORY_ID} = $data[self::ASSET_CATEGORY_ID];
        $this->{self::CODE} = $data[self::CODE];
        $this->{self::SIZE} = $data[self::SIZE];
        $this->{self::PRICE} = $data[self::PRICE];
        $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
    }

    //Gallery Photo Relationship
    public function galleryPhoto()
    {
        return $this->hasMany(GalleryPhoto::class, 'type_id', 'id')
            ->where('type', GalleryPhotoType::getPropertyAsset());
    }

    //list
    public static function lists($filter = [], $sort = '')
    {
        //Filter
        $propertyId = isset($filter['property_id']) ? $filter['property_id'] : null;
        $assetCategoryId = isset($filter['asset_category_id']) ? $filter['asset_category_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Price Rang
        $priceMin = isset($filter['price']['min']) ? floatval($filter['price']['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($filter['price']['max']) ? floatval($filter['price']['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort
        $newest = $sort == 'newest' ? 'newest' : null;
        $priceAsc = $sort == 'price_asc' ? 'price_asc' : null;
        $priceDsc = $sort == 'price_desc' ? 'price_desc' : null;

        return self::join('asset_category', 'asset_category.id', 'property_asset.asset_category_id')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('asset_category.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('property_asset.size', 'LIKE', '%' . $search . '%')
                        ->orWhere('property_asset.size', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($propertyId, function ($query) use ($propertyId) {
                $query->where('property_asset.business_id', $propertyId);
            })
            ->when($assetCategoryId, function ($query) use ($assetCategoryId) {
                $query->where('asset_category.id', $assetCategoryId);
            })
            ->when($newest, function ($query) {
                $query->orderBy("property_asset.id", "DESC");
            })
            ->when($priceMin, function ($query) use ($priceMin) {
                $query->where('property_asset.price', '>=', $priceMin);
            })
            ->when($priceMax, function ($query) use ($priceMax) {
                $query->where('property_asset.price', '<=', $priceMax);
            })
            ->when($priceAsc, function ($query) {
                $query->orderBy('property_asset.price');
            })
            ->when($priceDsc, function ($query) {
                $query->orderBy('property_asset.price', 'DESC');
            })
            ->select(
                'property_asset.id',
                'property_asset.business_id',
                'asset_category.id as asset_category_id',
                'asset_category.name as asset_category_name',
                'property_asset.code',
                'property_asset.size',
                'property_asset.price',
                'property_asset.image',
                'property_asset.status',
                'property_asset.active',
                'property_asset.description',
            )
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->orderBy('gallery_photo.order', 'ASC');
                }
            ]);
    }
}

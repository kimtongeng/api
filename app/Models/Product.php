<?php

namespace App\Models;

use App\Enums\Types\DiscountType;
use App\Enums\Types\GalleryPhotoType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'product';
    const ID = 'id';
    const COUNTRY_ID = 'country_id';
    const BUSINESS_ID = 'business_id';
    const CATEGORY_ID = 'category_id';
    const BRAND_ID = 'brand_id';
    const MODEL_ID = 'model_id';
    const CODE = 'code';
    const NAME = 'name';
    const PRICE = 'price';
    const IS_DISCOUNT = 'is_discount';
    const ORDER_COUNT = 'order_count';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_TYPE = 'discount_type';
    const SELL_PRICE = 'sell_price';
    const IS_TRACK_STOCK = 'is_track_stock';
    const QTY = 'qty';
    const DURATION = 'duration';
    const TYPE = 'type';
    const ALERT_QTY = 'alert_qty';
    const IMAGE = 'image';
    const PARENT_ID = 'parent_id';
    const HAS_VARIANT = 'has_variant';
    const DESCRIPTION = 'description';
    const VIDEO_LINK = 'video_link';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    /*
     * Relationship Area
     * */
    //Gallery Photo Relationship
    public function galleryPhoto()
    {
        return $this->hasMany(GalleryPhoto::class, GalleryPhoto::TYPE_ID, GalleryPhoto::ID);
    }

    //Product Modifier Relationship
    public function productModifier()
    {
        return $this->hasMany(ProductModifier::class, ProductModifier::PRODUCT_ID, self::ID)
            ->join('modifier', 'modifier.id', 'product_modifier.modifier_id');
    }

    //Product Variant Relationship
    public function productVariant()
    {
        return $this->hasMany(self::class, self::PARENT_ID, self::ID);
    }

    // Define the inverse relationship
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Define a custom accessor for the image
    public function getImageAttribute($value)
    {
        if ($this->parent_id && !$value) {
            return $this->parent->image;
        }

        return $value;
    }


    /*
     * Function Area
     * */
    //Set Data
    public function setData($data)
    {
        isset($data[self::COUNTRY_ID]) && $this->{self::COUNTRY_ID} = $data[self::COUNTRY_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        isset($data[self::CATEGORY_ID]) && $this->{self::CATEGORY_ID} = $data[self::CATEGORY_ID];
        isset($data[self::BRAND_ID]) && $this->{self::BRAND_ID} = $data[self::BRAND_ID];
        isset($data[self::MODEL_ID]) && $this->{self::MODEL_ID} = $data[self::MODEL_ID];
        isset($data[self::CODE]) && $this->{self::CODE} = $data[self::CODE];
        $this->{self::NAME} = $data[self::NAME];
        isset($data[self::PRICE]) && $this->{self::PRICE} = $data[self::PRICE];
        isset($data[self::IS_DISCOUNT]) && $this->{self::IS_DISCOUNT} = $data[self::IS_DISCOUNT];
        isset($data[self::ORDER_COUNT]) && $this->{self::ORDER_COUNT} = $data[self::ORDER_COUNT];
        isset($data[self::DISCOUNT_AMOUNT]) && $this->{self::DISCOUNT_AMOUNT} = $data[self::DISCOUNT_AMOUNT];
        isset($data[self::DISCOUNT_TYPE]) && $this->{self::DISCOUNT_TYPE} = $data[self::DISCOUNT_TYPE];
        isset($data[self::SELL_PRICE]) && $this->{self::SELL_PRICE} = $data[self::SELL_PRICE];
        isset($data[self::IS_TRACK_STOCK]) && $this->{self::IS_TRACK_STOCK} = $data[self::IS_TRACK_STOCK];
        isset($data[self::QTY]) && $this->{self::QTY} = $data[self::QTY];
        isset($data[self::DURATION]) && $this->{self::DURATION} = $data[self::DURATION];
        isset($data[self::TYPE]) && $this->{self::TYPE} = $data[self::TYPE];
        isset($data[self::ALERT_QTY]) && $this->{self::ALERT_QTY} = $data[self::ALERT_QTY];
        isset($data[self::PARENT_ID]) && $this->{self::PARENT_ID} = $data[self::PARENT_ID];
        isset($data[self::HAS_VARIANT]) && $this->{self::HAS_VARIANT} = $data[self::HAS_VARIANT];
        isset($data[self::DESCRIPTION]) && $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
        isset($data[self::VIDEO_LINK]) && $this->{self::VIDEO_LINK} = $data[self::VIDEO_LINK];
        isset($data[self::STATUS]) && $this->{self::STATUS} = $data[self::STATUS];
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

    //List Shop Product
    public static function lists($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $productID = isset($filter['product_id']) ? $filter['product_id'] : null;
        $categoryID = isset($filter['category_id']) ? $filter['category_id'] : null;
        $brandID = isset($filter['brand_id']) ? $filter['brand_id'] : null;
        $isDiscount = isset($filter['is_discount']) ? $filter['is_discount'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Price Range
        $priceMin = isset($filter['price']['min']) ? floatval($filter['price']['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($filter['price']['max']) ? floatval($filter['price']['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $priceAsc = $sortBy == 'price_asc' ? 'price_asc' : null;
        $priceDsc = $sortBy == 'price_desc' ? 'price_desc' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        return self::select(
            'product.id',
            'product.name',
            'product.code',
            'business.id as business_id',
            'business.name as business_name',
            'business.image as business_image',
            'category.id as category_id',
            'category.name as category_name',
            'category.parent_id as category_parent_id',
            'brand.id as brand_id',
            'brand.name as brand_name',
            'product.price',
            'product.is_discount',
            'product.order_count',
            'product.discount_amount',
            'product.discount_type',
            'product.sell_price',
            'product.is_track_stock',
            'product.qty',
            'product.alert_qty',
            'product.image',
            'product.has_variant',
            'product.created_at',
            'product.status',
            'product.description',
            'product.video_link',
        )
            ->join('business', 'business.id', 'product.business_id')
            ->join('category', 'category.id', 'product.category_id')
            ->leftjoin('brand', 'brand.id', 'product.brand_id')
            ->leftjoin('country', 'country.id', 'product.country_id')
            ->whereNull('product.parent_id')
            ->when($businessTypeID, function ($query) use ($businessTypeID) {
                $query->where('business.business_type_id', $businessTypeID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('product.business_id', $businessID);
            })
            ->when($productID, function ($query) use ($productID) {
                $query->where('product.id', $productID);
            })
            ->when($categoryID, function ($query) use ($categoryID) {
                $query->where(function ($query) use ($categoryID) {
                    $query->where('product.category_id', $categoryID)
                        ->orWhere('category.parent_id', $categoryID);
                });
            })
            ->when($brandID, function ($query) use ($brandID) {
                $query->where('product.brand_id', $brandID);
            })
            ->when($isDiscount, function ($query) use ($isDiscount) {
                $query->where('product.is_discount', $isDiscount);
            })
            ->when($priceMin, function ($query) use ($priceMin) {
                $query->where('product.sell_price', '>=', $priceMin);
            })
            ->when($priceMax, function ($query) use ($priceMax) {
                $query->where('product.sell_price', '<=', $priceMax);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('product.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('product.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($newest, function ($query) {
                $query->orderBy("product.id", "DESC");
            })
            ->when($priceAsc, function ($query) {
                $query->orderBy('product.sell_price');
            })
            ->when($priceDsc, function ($query) {
                $query->orderBy('product.sell_price', 'DESC');
            })
            ->when($orderCount, function ($query) {
                $query->orderBy('product.order_count', 'DESC');
            })
            ->with([
                'galleryPhoto' => function ($query) {
                    $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getShopProduct())
                        ->orderBy('gallery_photo.order', 'ASC');
                },
                'productModifier' => function ($query) {
                    $query->select(
                        'product_modifier.*',
                        'modifier.id',
                        'modifier.business_id',
                        'modifier.name',
                        'modifier.choice',
                        'modifier.is_required',
                        'modifier.description'
                    )
                        ->with([
                            'modifierOption' => function ($query) {
                                $query->select(
                                    'modifier_option.id',
                                    'modifier_option.modifier_id',
                                    'modifier_option.name',
                                    'modifier_option.price',
                                )
                                    ->orderBy('modifier_option.id', 'DESC')
                                    ->get();
                            }
                        ])
                        ->orderBy('product_modifier.id', 'desc')
                        ->get();
                },
                'productVariant' => function ($query) {
                    $query->select(
                        'product.id',
                        'product.parent_id',
                        'product.name',
                        'product.image',
                        'product.price',
                        'product.is_discount',
                        'product.discount_amount',
                        'product.discount_type',
                        'product.sell_price',
                        'product.is_track_stock',
                        'product.qty',
                        'product.alert_qty',
                    )
                    ->orderBy('product.id', 'desc')
                    ->get();
                },
            ]);
    }

    // List Massage Service
    public static function  listMassageService($filter = [], $sortBy = "")
    {
        //Filter
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $serviceID = isset($filter['service_id']) ? $filter['service_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Price Range
        $priceMin = isset($filter['price']['min']) ? floatval($filter['price']['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($filter['price']['max']) ? floatval($filter['price']['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;
        $priceAsc = $sortBy == 'price_asc' ? 'price_asc' : null;
        $priceDsc = $sortBy == 'price_desc' ? 'price_desc' : null;
        $orderCount = $sortBy == 'best_selling' ? 'best_selling' : null;

        return self::select(
            'product.id',
            'product.name',
            'business.id as business_id',
            'business.name as business_name',
            'product.price',
            'product.image',
            'product.is_discount',
            'product.order_count',
            'product.discount_amount',
            'product.discount_type',
            'product.sell_price',
            'product.duration',
            'product.type',
            'product.description',
            'product.status',
            'product.created_at',
        )
        ->join('business', 'business.id', 'product.business_id')
        ->leftjoin('country', 'country.id', 'product.country_id')
        ->when($businessID, function ($query) use ($businessID) {
            $query->where('product.business_id', $businessID);
        })
        ->when($serviceID, function ($query) use ($serviceID) {
            $query->where('product.id', $serviceID);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('product.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('product.code', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($newest, function ($query) {
            $query->orderBy("product.id", "DESC");
        })
        ->with([
            'galleryPhoto' => function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getMassagerService())
                    ->orderBy('gallery_photo.order', 'ASC');
            }
        ]);
    }

    public static function listsKTV($filter = [], $sortBy = "")
    {
        //Filter
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $productID = isset($filter['product_id']) ? $filter['product_id'] : null;
        $categoryID = isset($filter['category_id']) ? $filter['category_id'] : null;
        $isDiscount = isset($filter['is_discount']) ? $filter['is_discount'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Sort Mobile
        $newest = $sortBy == 'newest' ? 'newest' : null;

        return self::select(
            'product.id',
            'product.name',
            'business.id as business_id',
            'category.id as category_id',
            'category.name as category_name',
            'product.price',
            'product.is_discount',
            'product.discount_amount',
            'product.discount_type',
            'product.sell_price',
            'product.image',
            'product.created_at',
            'product.status',
            'product.description',
        )
        ->join('business', 'business.id', 'product.business_id')
        ->join('category', 'category.id', 'product.category_id')
        ->when($businessID, function ($query) use ($businessID) {
            $query->where('product.business_id', $businessID);
        })
        ->when($productID, function ($query) use ($productID) {
            $query->where('product.id', $productID);
        })
        ->when($categoryID, function ($query) use ($categoryID) {
            $query->where(function ($query) use ($categoryID) {
                $query->where('product.category_id', $categoryID);
            });
        })
        ->when($isDiscount, function ($query) use ($isDiscount) {
            $query->where('product.is_discount', $isDiscount);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('product.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($newest, function ($query) {
            $query->orderBy("product.id", "DESC");
        });
    }
}

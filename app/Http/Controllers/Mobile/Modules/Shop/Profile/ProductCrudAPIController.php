<?php

namespace App\Http\Controllers\Mobile\Modules\Shop\Profile;

use App\Enums\Types\DiscountType;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\IsDiscount;
use App\Enums\Types\IsHasVariant;
use App\Enums\Types\IsTrackStock;
use App\Enums\Types\ProductStatus;
use App\Models\Business;
use App\Models\GalleryPhoto;
use App\Models\Product;
use App\Models\ProductModifier;
use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductCrudAPIController extends Controller
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
            'code' => 'required',
            'price' => $data['has_variant'] == IsHasVariant::getNo() ? 'required' : 'nullable',
            'discount_amount' => $data['has_variant'] == IsHasVariant::getNo() ? 'required' : 'nullable',
            'discount_type' => $data['has_variant'] == IsHasVariant::getNo() ? 'required' : 'nullable',
            'category_id' => 'required|exists:category,id',
            'brand_id' => !empty($data['brand_id']) ? 'required|exists:brand,id' : 'nullable',
            'modifier_list' => !empty($data['modifier_list']) ? 'required' : 'nullable',
            'modifier_list.*.id' => !empty($data['modifier_list']) ? 'required|exists:modifier,id' : 'nullable',
            'description' => 'nullable',
            'status' => 'required',
            'is_track_stock' => 'required',
            'has_variant' => 'required',
            'qty' => $data['is_track_stock'] == IsTrackStock::getYes() && $data['has_variant'] == IsHasVariant::getNo() ? 'required' : 'nullable',
            'alert_qty' => $data['is_track_stock'] == IsTrackStock::getYes() && $data['has_variant'] == IsHasVariant::getNo() ? 'required' : 'nullable',
            'variant_list' => $data['has_variant'] == IsHasVariant::getYes() ? 'required' : 'nullable',
            'variant_list.*.name' => !empty($data['variant_list']) ? 'required' : 'nullable',
            'variant_list.*.image' => 'nullable',
            'variant_list.*.old_image' => 'nullable',
            'variant_list.*.price' => !empty($data['variant_list']) ? 'required' : 'nullable',
            'variant_list.*.discount_amount' => !empty($data['variant_list']) ? 'required' : 'nullable',
            'variant_list.*.discount_type' => !empty($data['variant_list']) ? 'required' : 'nullable',
            'variant_list.*.qty' => !empty($data['variant_list']) && $data['is_track_stock'] == IsTrackStock::getYes() ? 'required' : 'nullable',
            'variant_list.*.alert_qty' => !empty($data['variant_list']) && $data['is_track_stock'] == IsTrackStock::getYes() ? 'required' : 'nullable',
            'deleted_variant_list.*.id' => !empty($data['id']) && $data['has_variant'] == IsHasVariant::getYes() && !empty($data['deleted_variant_list']) ? 'required|exists:product,id' : 'nullable',
        ]);
    }

    /**
     * Add Product
     *
     */
    public function addProduct(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        //Merge Value Some Request
        $request->merge([
            Product::COUNTRY_ID => Business::find($request->input('business_id'))->{Business::COUNTRY_ID},
        ]);

        //Set Data
        $product = new Product();
        $product->setData($request);
        $product->parent_id = null;
        $product->created_at = Carbon::now();

        //Save Data
        if ($product->save()) {
            //Upload Image
            if (!empty($request->input('image'))) {
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::shopProductThumb);
                $product->image = $image;
                $product->save();
            }

            //Upload Gallery Photo
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getShopProduct(),
                        GalleryPhoto::TYPE_ID => $product->id,
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::shopProductGallery);
                        $gallery_photo->image = $image;
                        $gallery_photo->save();
                    }
                }
            }

            //Set Discount
            if ($request->input('has_variant') == IsHasVariant::getNo()) {
                $isDiscount = IsDiscount::getNo();
                $discountAmount = floatval($request->input('discount_amount'));
                $discountType = null;
                if ($discountAmount > 0) {
                    $isDiscount = IsDiscount::getYes();
                    $discountType = $request->input('discount_type');
                }

                $product->is_discount = $isDiscount;
                $product->discount_amount = $discountAmount;
                $product->discount_type = $discountType;
                $sellPrice = $product->getSellPrice();
                $product->sell_price = $sellPrice;
                $product->save();
            }

            //Set Product Modifier Data
            if (!empty($request->input('modifier_list'))) {
                foreach ($request->input('modifier_list') as $obj) {
                    $data = [
                        ProductModifier::PRODUCT_ID => $product->id,
                        ProductModifier::MODIFIER_ID => $obj['id']
                    ];

                    $product_modifier = new ProductModifier();
                    $product_modifier->setData($data);
                    $product_modifier->save();
                }
            }

            //Set Stock
            $qty = $request->input('is_track_stock') == IsTrackStock::getYes() && $request->input('has_variant') == IsHasVariant::getNo() ? $request->input('qty') : 0;
            $alertQty = $request->input('is_track_stock') == IsTrackStock::getYes() && $request->input('has_variant') == IsHasVariant::getNo() ? $request->input('alert_qty') : 0;
            $product->qty = $qty;
            $product->alert_qty = $alertQty;
            $product->save();


            //Set Variant
            if ($request->input('has_variant') == IsHasVariant::getYes() && !empty($request->input('variant_list'))) {
                foreach ($request->input('variant_list') as $key => $obj) {
                    $dataVariant = [
                        'name' => $obj['name'],
                        'price' => $obj['price'],
                        'parent_id' => $product->id,
                        'has_variant' => IsHasVariant::getNo(),
                        'is_track_stock' => $product->is_track_stock,
                        'country_id' => $product->country_id,
                        'business_id' => $product->business_id,
                        'category_id' => $product->category_id,
                        'brand_id' => $product->brand_id,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at
                    ];

                    //Set Variant Data
                    $productVariant = new Product();
                    $productVariant->setData($dataVariant);
                    if ($productVariant->save()) {
                        //Upload Image
                        if (!empty($obj['image'])) {
                            $image = StringHelper::uploadImage($obj['image'], ImagePath::shopProductThumb);
                            $productVariant->image = $image;
                            $productVariant->save();
                        }

                        //Set Discount
                        $isDiscountVariant = IsDiscount::getNo();
                        $discountAmountVariant = floatval($obj['discount_amount']);
                        $discountTypeVariant = null;
                        if ($discountAmountVariant > 0) {
                            $isDiscountVariant = IsDiscount::getYes();
                            $discountTypeVariant = $obj['discount_type'];
                        }

                        $productVariant->is_discount = $isDiscountVariant;
                        $productVariant->discount_amount = $discountAmountVariant;
                        $productVariant->discount_type = $discountTypeVariant;
                        $sellPriceVariant = $productVariant->getSellPrice();
                        $productVariant->sell_price = $sellPriceVariant;
                        $productVariant->save();

                        //Set Stock
                        $qtyVariant = $request->input('is_track_stock') == IsTrackStock::getYes() ? $obj['qty'] : 0;
                        $alertQtyVariant = $request->input('is_track_stock') == IsTrackStock::getYes() ? $obj['alert_qty'] : 0;
                        $productVariant->qty = $qtyVariant;
                        $productVariant->alert_qty = $alertQtyVariant;
                        $productVariant->save();

                        //Set Price And Discount to main product by variant index 0
                        if ($key == 0) {
                            $product->price = $productVariant->price;
                            $product->is_discount = $productVariant->is_discount;
                            $product->discount_amount = $productVariant->discount_amount;
                            $product->discount_type = $productVariant->discount_type;
                            $product->sell_price = $productVariant->sell_price;
                            $product->save();
                        }
                    } else {
                        return $this->responseValidation(ErrorCode::ACTION_FAILED);
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
     * Edit Product
     *
     */
    public function editProduct(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $product = Product::find($request->input('id'));

        if (!empty($product)) {
            //Set Data
            $product->setData($request);
            $product->parent_id = null;
            $product->updated_at = Carbon::now();

            //Save Data
            if ($product->save()) {
                //Update Image
                if (!empty($request->input('image'))) {
                    $image = StringHelper::editImage(
                        $request->input('image'),
                        $request->input('old_image'),
                        ImagePath::shopProductThumb
                    );
                    $product->image = $image;
                    $product->save();
                }

                //Upload Or Update Gallery Photo
                if (!empty($request->input('gallery_photo'))) {
                    foreach ($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getShopProduct(),
                                GalleryPhoto::TYPE_ID => $product->{Product::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo = new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Image
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::shopProductGallery);
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
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::shopProductGallery);
                        }
                    }
                }

                //Set Discount
                if ($request->input('has_variant') == IsHasVariant::getNo()) {
                    $isDiscount = IsDiscount::getNo();
                    $discountAmount = floatval($request->input('discount_amount'));
                    $discountType = null;
                    if ($discountAmount > 0) {
                        $isDiscount = IsDiscount::getYes();
                        $discountType = $request->input('discount_type');
                    }

                    $product->is_discount = $isDiscount;
                    $product->discount_amount = $discountAmount;
                    $product->discount_type = $discountType;
                    $sellPrice = $product->getSellPrice();
                    $product->sell_price = $sellPrice;
                    if ($product->save()) {
                        //Delete Variant
                        if ($product->has_variant == IsHasVariant::getYes()) {
                            $productVariant = Product::where('parent_id', $product->id);
                            if ($productVariant->delete()) {
                                //Delete Thumbnail
                                StringHelper::deleteImage($productVariant->{Product::IMAGE}, ImagePath::shopProductThumb);
                            }
                        }
                    }
                }

                //Set Product Modifier Data
                if (!empty($request->input('modifier_list'))) {
                    //Delete All Product Modifier
                    ProductModifier::where(ProductModifier::PRODUCT_ID, $product->id)->forceDelete();

                    foreach ($request->input('modifier_list') as $obj) {
                        $data = [
                            ProductModifier::PRODUCT_ID => $product->id,
                            ProductModifier::MODIFIER_ID => $obj['id']
                        ];

                        $product_modifier = new ProductModifier();
                        $product_modifier->setData($data);
                        $product_modifier->save();
                    }
                }

                //Set Stock
                $qty = $request->input('is_track_stock') == IsTrackStock::getYes() && $request->input('has_variant') == IsHasVariant::getNo() ? $request->input('qty') : 0;
                $alertQty = $request->input('is_track_stock') == IsTrackStock::getYes() && $request->input('has_variant') == IsHasVariant::getNo() ? $request->input('alert_qty') : 0;
                $product->qty = $qty;
                $product->alert_qty = $alertQty;
                $product->save();


                //Set, Update Or Delete Variant
                if ($request->input('has_variant') == IsHasVariant::getYes()) {
                    //Set Or Update Variant
                    if (!empty($request->input('variant_list'))) {
                        foreach ($request->input('variant_list') as $key => $obj) {
                            $dataVariant = [
                                'name' => $obj['name'],
                                'price' => $obj['price'],
                                'parent_id' => $product->id,
                                'has_variant' => IsHasVariant::getNo(),
                                'is_track_stock' => $product->is_track_stock,
                                'country_id' => $product->country_id,
                                'business_id' => $product->business_id,
                                'category_id' => $product->category_id,
                                'brand_id' => $product->brand_id,
                                'created_at' => $product->created_at,
                                'updated_at' => $product->updated_at
                            ];

                            //Set Variant Data
                            if (empty($obj['id'])) {
                                $productVariant = new Product();
                            } else {
                                $productVariant = Product::find($obj['id']);
                            }

                            $productVariant->setData($dataVariant);

                            if ($productVariant->save()) {
                                //Update Image
                                if (!empty($obj['image'])) {
                                    $image = StringHelper::editImage(
                                        $obj['image'],
                                        $obj['old_image'],
                                        ImagePath::shopProductThumb
                                    );
                                    $productVariant->image = $image;
                                    $productVariant->save();
                                }
                                //Set Discount
                                $isDiscountVariant = IsDiscount::getNo();
                                $discountAmountVariant = floatval($obj['discount_amount']);
                                $discountTypeVariant = null;
                                if ($discountAmountVariant > 0) {
                                    $isDiscountVariant = IsDiscount::getYes();
                                    $discountTypeVariant = $obj['discount_type'];
                                }

                                $productVariant->is_discount = $isDiscountVariant;
                                $productVariant->discount_amount = $discountAmountVariant;
                                $productVariant->discount_type = $discountTypeVariant;
                                $sellPriceVariant = $productVariant->getSellPrice();
                                $productVariant->sell_price = $sellPriceVariant;
                                $productVariant->save();

                                //Set Stock
                                $qtyVariant = $request->input('is_track_stock') == IsTrackStock::getYes() ? $obj['qty'] : 0;
                                $alertQtyVariant = $request->input('is_track_stock') == IsTrackStock::getYes() ? $obj['alert_qty'] : 0;
                                $productVariant->qty = $qtyVariant;
                                $productVariant->alert_qty = $alertQtyVariant;
                                $productVariant->save();

                                //Set Price And Discount to main product by variant index 0
                                if ($key == 0) {
                                    $product->price = $productVariant->price;
                                    $product->is_discount = $productVariant->is_discount;
                                    $product->discount_amount = $productVariant->discount_amount;
                                    $product->discount_type = $productVariant->discount_type;
                                    $product->sell_price = $productVariant->sell_price;
                                    $product->save();
                                }
                            } else {
                                return $this->responseValidation(ErrorCode::ACTION_FAILED);
                            }

                        }
                    }

                    //Delete Variant
                    if (!empty($request->input('deleted_variant_list'))) {
                        foreach ($request->input('deleted_variant_list') as $item) {
                            $productVariant = Product::find($item['id']);
                            if($productVariant->delete()) {
                                //Delete Thumbnail
                                StringHelper::deleteImage($productVariant->{Product::IMAGE}, ImagePath::shopProductThumb);
                            }
                        }
                    }
                }

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
     * Delete Product
     *
     */
    public function deleteProduct(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:product,id'
        ]);

        DB::beginTransaction();

        $product = Product::find($request->input('id'));

        if ($product->delete()) {
            //Delete Thumbnail
            StringHelper::deleteImage($product->{Product::IMAGE}, ImagePath::shopProductThumb);

            //Delete Gallery Photo Single and Multi
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getShopProduct())
                ->where(GalleryPhoto::TYPE_ID, $product->{Product::ID})
                ->get();
            foreach ($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::shopProductGallery);
                $gallery_photo->delete();
            }


            //Delete Product Modifier
            ProductModifier::where(ProductModifier::PRODUCT_ID, $product->id)->forceDelete();

            //Delete Variant
            if ($product->{Product::HAS_VARIANT} == IsHasVariant::getYes()) {
                $productVariant = Product::where(Product::PARENT_ID, $product->id);
                if ($productVariant->delete()) {
                    //Delete Thumbnail
                    StringHelper::deleteImage($productVariant->{Product::IMAGE}, ImagePath::shopProductThumb);
                }
            }
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get List Product
     *
     */
    public function getMyProduct(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Product::lists($filter, $sort)
        ->where('product.status','!=',ProductStatus::getSuspend())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     *Set Product Qty
     *
     */
    public function updateProductQty(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:product,id',
            'qty' => 'required',
        ]);

        $product = Product::find($request->input('product_id'));
        $newQty = $product->qty + $request->input('qty');

        $update = DB::table('product')
        ->where('id', $request->input('product_id'))
        ->update([
            'qty' => $newQty,
            'updated_at' => Carbon::now()
        ]);

        if ($update) {
            $response = ['qty' => $newQty];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

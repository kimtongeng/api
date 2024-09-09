<?php

namespace App\Http\Controllers\Mobile\Modules\Shop\Profile;

use App\Models\Product;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Enums\Types\IsHasVariant;
use App\Enums\Types\IsTrackStock;
use App\Enums\Types\ProductStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\GalleryPhotoType;

class ProductListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Product Filter Sort
     *
     */
    public function getProductFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Product::lists($filter,$sort)
        ->where('product.status', ProductStatus::getEnabled())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Product Detail
     */
    public function getProductDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.product_id' => 'required',
        ]);

        $filter = $request->input('filter');

        $data = Product::lists($filter)->first();

        return $this->responseWithData($data);
    }

    /**
     * Check stock Product list
     */
    public function getProductStockReport(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required',
            'filter' => 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $businessID = $request->input('business_id');
        $filter = $request->input('filter');
        // Filter
        $categoryID = isset($filter['category_id']) ? $filter['category_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        $data = Product::join('business', 'business.id', 'product.business_id')
            ->join('category', 'category.id', 'product.category_id')
            ->leftjoin('brand', 'brand.id', 'product.brand_id')
            ->leftjoin('country', 'country.id', 'product.country_id')
            ->leftjoin('product as main', 'main.id', 'product.parent_id')
            ->where('product.business_id', $businessID)
            ->where('product.qty', '<=', DB::raw('product.alert_qty'))
            ->where('product.alert_qty', '>', 0)
            ->whereNull('product.deleted_at')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('product.has_variant', IsHasVariant::getNo())
                        ->whereNull('product.parent_id')
                        ->where('product.is_track_stock', IsTrackStock::getYes());
                })
                ->orWhere(function ($query) {
                    $query->whereNotNull('product.parent_id')
                        ->where('product.is_track_stock', IsTrackStock::getYes());
                });
            })
            ->when($categoryID, function ($query) use ($categoryID) {
                $query->where(function ($query) use ($categoryID) {
                    $query->where('product.category_id', $categoryID)
                        ->orWhere('category.parent_id', $categoryID);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('product.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('product.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
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
                // Use COALESCE to get the main product image if the variant image is null
                DB::raw('COALESCE(product.image, main.image) as product_image'),
                'product.has_variant',
                'product.parent_id',
                'product.created_at',
                'product.status',
                'main.id as main_id',
                'main.name as main_name',
                'product.description',
            )
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
            ])
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

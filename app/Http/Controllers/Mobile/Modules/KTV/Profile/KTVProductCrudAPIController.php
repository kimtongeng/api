<?php

namespace App\Http\Controllers\Mobile\Modules\KTV\Profile;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Enums\Types\IsDiscount;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Enums\Types\ProductStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KTVProductCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Validation
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:product,id' : 'nullable',
            'business_id' => 'required',
            'category_id' => 'required',
            'name' => 'required',
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            'price' => 'required',
            'discount_amount' => 'nullable',
            'discount_type' => 'nullable',
            'sell_price' => 'nullable',
            'description' => 'nullable',
            'status' => 'required',
        ]);
    }

    /**
     * Add KTV Product
     */
    public function addKTVProduct(Request $request)
    {
        //Check Validation
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
        if($product->save()) {
            //Upload Image
            if (!empty($request->input('image'))) {
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::ktvProductThumb);
                $product->image = $image;
                $product->save();
            }

            //Set Discount
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

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit KTV Product
     */
    public function editKTVProduct(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $product = Product::find($request->input('id'));

        if(!empty($product)) {
            //Set Data
            $product->setData($request);
            $product->parent_id = null;
            $product->updated_at = Carbon::now();

            if($product->save()) {
                //Update Image
                if (!empty($request->input('image'))) {
                    $image = StringHelper::editImage(
                        $request->input('image'),
                        $request->input('old_image'),
                        ImagePath::ktvProductThumb
                    );
                    $product->image = $image;
                    $product->save();
                }

                //Set Discount
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
     * Delete KTV Product
     */
    public function deleteKTVProduct(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:product,id'
        ]);

        DB::beginTransaction();

        $product = Product::find($request->input('id'));

        if($product->delete()) {
            //Delete Thumbnail
            StringHelper::deleteImage($product->{Product::IMAGE}, ImagePath::ktvProductThumb);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get KTV Product List
     */
    public function getMyKTVProduct(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Product::listsKTV($filter, $sort)
        ->where('product.status', ProductStatus::getEnabled())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\AdjustItemQuantityType;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\IsTrackStock;
use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartModifier;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function response;

class BusinessCardAPIController extends Controller
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
            'id' => !empty($data['id']) ? 'required|exists:cart,id' : 'nullable',
            'business_type_id' => 'required|exists:business_type,id',
            'business_id' => 'required|exists:business,id',
            'contact_id' => 'required|exists:contact,id',
            'item_id' => 'required',
            'qty' => 'required',
            'cart_modifier' => 'nullable',
            'cart_modifier.*.product_modifier_id' => 'required|exists:modifier,id',
            'cart_modifier.*.product_modifier_option_id' => 'required|exists:modifier_option,id',
        ]);
    }

    /**
     * Add To Cart
     *
     */

    public function addItemToCart(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        //Check Stock Product if Business Type Shop
        if ($request->input('business_type_id') == BusinessTypeEnum::getShopRetail() ||
            $request->input('business_type_id') == BusinessTypeEnum::getShopWholesale() ||
            $request->input('business_type_id') == BusinessTypeEnum::getRestaurant() ||
            $request->input('business_type_id') == BusinessTypeEnum::getShopLocalProduct() ||
            $request->input('business_type_id') == BusinessTypeEnum::getService() ||
            $request->input('business_type_id') == BusinessTypeEnum::getModernCommunity()
        ) {
            $productData = Product::where('id', $request->input('item_id'))
            ->where(function ($query) {
                $query->where('is_track_stock', IsTrackStock::getYes())
                ->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_track_stock', IsTrackStock::getYes());
                });
            })
            ->first();

            if (!empty($productData)) {
                $currentQty = floatval($productData->qty);
                if ($currentQty == 0) {
                    return response()->json([
                        'message' => 'This Product is out of stock'
                    ], 422);
                } else if ($currentQty < $request->input('qty')) {
                    return response()->json([
                        'message' => 'The Product is inadequate for order'
                    ], 422);
                }
            }

        }

        //Check current user have in cart or not
        $cartByContact = Cart::where('contact_id', $request->input('contact_id'))->first();
        if (!empty($cartByContact) && !empty($request->input('yes'))) {
            //Check if have in cart have same shop or not
            if ($cartByContact->business_id != $request->input('business_id')) {
                // Find all carts by contact_id
                $carts = Cart::where('contact_id', $request->input('contact_id'))->get();

                // Loop through each cart and delete
                foreach ($carts as $cart) {
                    // Delete the cart
                    if ($cart->delete()) {
                        // Delete Cart Modifier List
                        CartModifier::where(CartModifier::CART_ID, $cart->{Cart::ID})->delete();
                    }
                }
            }
        }

        $modifierOption = [];
        if (!empty($request->input('cart_modifier'))) {
            foreach ($request->input('cart_modifier') as $obj) {
                $modifierOption[] = $obj['product_modifier_option_id'];
            }
        }

        $cartData = Cart::leftjoin('cart_modifier', 'cart.id', '=', 'cart_modifier.cart_id')
        ->select(
            'cart.*',
            DB::raw('GROUP_CONCAT(cart_modifier.product_modifier_option_id ORDER BY cart_modifier.product_modifier_option_id DESC) AS modifier_option')
        )
        ->where('cart.business_id', '=', $request->input('business_id'))
        ->where('cart.item_id', '=', $request->input('item_id'))
        ->where('cart.contact_id', $request->input('contact_id'))
        ->groupBy('cart.id')
        ->when(!empty($request->input('cart_modifier')), function ($query) use ($modifierOption) {
            $query->having("modifier_option", '=', implode(',', $modifierOption));
        })
        ->get();

        if (count($cartData) > 0) {
            // Update quantity of the existing item
            $existingCart = $cartData->first();
            $existingCart->qty += $request->input('qty');
            $productData = Product::where('id', $request->input('item_id'))
            ->where(function ($query) {
                $query->where('is_track_stock', IsTrackStock::getYes())
                ->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_track_stock', IsTrackStock::getYes());
                });
            })
            ->first();

            if (!empty($productData)) {
                $currentQty = floatval($productData->qty);
                if ($currentQty == 0) {
                    return response()->json([
                        'message' => 'This Product is out of stock'
                    ], 422);
                } else if ($currentQty < $existingCart->qty) {
                    return response()->json([
                        'message' => 'The Product is inadequate for order'
                    ], 422);
                }
            }

            $existingCart->save();

            return $this->responseWithSuccess();
        } else {
            //Set Cart Data
            $cart = new Cart();
            $cart->setData($request);
            $cart->created_at = Carbon::now();

            if ($cart->save()) {
                //Set Data Cart Modifier
                if (!empty($request->input('cart_modifier'))) {
                    foreach ($request->input('cart_modifier') as $obj) {
                        $cart_modifier_list = [
                            CartModifier::CART_ID => $cart->{Cart::ID},
                            CartModifier::PRODUCT_MODIFIER_ID => $obj['product_modifier_id'],
                            CartModifier::PRODUCT_MODIFIER_OPTION_ID => $obj['product_modifier_option_id']
                        ];
                        $cart_modifier = new CartModifier();
                        $cart_modifier->setData($cart_modifier_list);
                        $cart_modifier->save();
                    }
                }
                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }
    }

    /**
     *  Adjust Quantity
     *
     */
    public function adjustItemQuantity(Request $request)
    {
        //Check Stock Product if Business Type Shop
        if ($request->input('business_type_id') == BusinessTypeEnum::getShopRetail() ||
            $request->input('business_type_id') == BusinessTypeEnum::getShopWholesale() ||
            $request->input('business_type_id') == BusinessTypeEnum::getRestaurant() ||
            $request->input('business_type_id') == BusinessTypeEnum::getShopLocalProduct() ||
            $request->input('business_type_id') == BusinessTypeEnum::getService() ||
            $request->input('business_type_id') == BusinessTypeEnum::getModernCommunity()
        ) {
            $productData = Product::where('id', $request->input('item_id'))
                ->where(function ($query) {
                    $query->where('is_track_stock', IsTrackStock::getYes())
                        ->orWhere(function ($query) {
                            $query->whereNotNull('parent_id')
                                ->where('is_track_stock', IsTrackStock::getYes());
                        });
                })
                ->first();

            if (!empty($productData)) {
                $currentQty = floatval($productData->qty);
                if ($currentQty == 0) {
                    return response()->json([
                        'message' => 'This Product is out of stock'
                    ], 422);
                }
            }

        }

        $cart = Cart::find($request->input(Cart::ID));
        $action = $request->input('action');

        if ($action == AdjustItemQuantityType::getDecrement()) {
            if (floatval($cart->qty) == 1) {
                // Find the cart by ID
                $cartToDelete = Cart::find($request->input('id'));

                if (!is_null($cartToDelete)) {
                    // Delete the cart
                    if ($cartToDelete->delete()) {
                        // Delete Cart Modifier List
                        CartModifier::where(CartModifier::CART_ID, $cartToDelete->{Cart::ID})->delete();
                    }
                }
            } else {
                $cart->qty -= 1;
            }
        } else if ($action == AdjustItemQuantityType::getIncrement()) {
            $cart->qty +=1;
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }

        $productData = Product::where('id', $request->input('item_id'))
        ->where(function ($query) {
            $query->where('is_track_stock', IsTrackStock::getYes())
            ->orWhere(function ($query) {
                $query->whereNotNull('parent_id')
                    ->where('is_track_stock', IsTrackStock::getYes());
            });
        })
        ->first();

        if (!empty($productData)) {
            $currentQty = floatval($productData->qty);
            if ($currentQty == 0) {
                return response()->json([
                    'message' => 'This Product is out of stock'
                ], 422);
            } else if ($cart->qty > $currentQty) {
                return response()->json([
                    'message' => 'The Product is inadequate for order'
                ], 422);
            }
        }

        if ($cart->save()) {
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Remove Product From Cart
     *
     */
    public function removeItemFromCart(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:cart,id',
        ]);

        DB::beginTransaction();

        $cart = Cart::find($request->input(Cart::ID));
        if($cart->delete()) {
            //Delete Cart Modifier List
            CartModifier::where(CartModifier::CART_ID, $cart->{Cart::ID})->delete();
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get Cart List
     *
     */
    public function getCartList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
            'filter.contact_id' => 'required|exists:contact,id',
        ]);

        // Remove cart items with deleted products
        Cart::leftJoin('product', 'cart.item_id', '=', 'product.id')
        ->whereNotNull('product.deleted_at')
        ->delete();

        $tableSize = !empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');

        $data = Cart::listCart($filter)->paginate($tableSize);

        $totalAmount = 0;
        foreach ($data as $obj) {
            $totalAmount += floatval($obj->sub_total) * floatval($obj->qty);
        }

        $response = [
            'pagination' => [
                'total' => intval($data->total()),
                'per_page' => intval($data->perPage()),
                'current_page' => intval($data->currentPage()),
                'last_page' => intval($data->lastPage()),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            'total_amount' => number_format($totalAmount, 2),
            'data' => $data->items()
        ];

        return $this->responseWithData($response);
    }
}

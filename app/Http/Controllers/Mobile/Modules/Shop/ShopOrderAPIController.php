<?php

namespace App\Http\Controllers\Mobile\Modules\Shop;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Business;
use App\Models\PrefixCode;
use App\Models\Transaction;
use App\Models\BusinessType;
use App\Models\CartModifier;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\GeneralSetting;
use App\Enums\Types\AppTypeEnum;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Models\ProductOrderList;
use App\Enums\Types\IsTrackStock;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\TransactionStatus;
use App\Models\ProductOrderModifierOption;
use App\Enums\Types\ContactNotificationType;
use App\Models\BusinessPermission;
use App\Models\BusinessShareContact;

class ShopOrderAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Stock for Multiple Products
     *
     */
    public function checkStockProduct(Request $request)
    {
        // Validate the request input
        $this->validate($request, [
            'product' => 'required',
            'product.*.id' => 'required|exists:product,id',
            'product.*.qty' => 'required',
        ]);

        $message = [];

        // Loop through each product in the request
        foreach ($request->input('product') as $product) {
            $productData = Product::where('id', $product['id'])
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
                    $message[] = [
                        'id' => $product['id'],
                        'name' => $productData['name'],
                        'message' => 'This Product is out of stock'
                    ];
                } else if ($currentQty < $product['qty']) {
                    $message[] = [
                        'id' => $product['id'],
                        'name' => $productData['name'],
                        'message' => 'The Product is inadequate for order'
                    ];
                }
            }
        }

        if (!empty($message)) {
            return response()->json($message, 422);
        }
        return $this->responseWithSuccess();
    }


    //Product Checkout
    public function productOrderCheckout(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id',
            'shop_id' => 'required|exists:business,id',
            'shop_owner_id' => 'required|exists:contact,id',
            'order_type' => 'required|numeric|min:1|max:6',
            'payment_type' => 'required',
            'customer_id' => 'required',
            'shipping_address_id' => 'nullable',
            'bank_account_id' => 'nullable',
            'transaction_date' => 'nullable',
            'total_amount' => 'nullable',
            'sell_amount' => 'required',
            'image' => 'nullable',
            //invoice Detail
            'fullname' => 'nullable',
            'phone' => 'nullable',
            'remark_booking' => 'nullable',
            //product_order_list
            'product_order_list' => 'required',
            'product_order_list.*.product_id' => 'required',
            'product_order_list.*.qty' => 'required',
            'product_order_list.*.price' => 'required',
            'product_order_list.*.concat_modifier' => 'nullable',
            //product_order_modifier_option
            'product_order_list.*product_order_modifier_option.*.modifier_id' => !empty($request['product_order_modifier_option']) ? 'required' : 'nullable',
            'product_order_list.*product_order_modifier_option.*.modifier_name' => !empty($request['product_order_modifier_option']) ? 'required' : 'nullable',
            'product_order_list.*product_order_modifier_option.*.modifier_option_id' => !empty($request['product_order_modifier_option']) ? 'required' : 'nullable',
            'product_order_list.*product_order_modifier_option.*.modifier_option_name' => !empty($request['product_order_modifier_option']) ? 'required' : 'nullable',
            'product_order_list.*product_order_modifier_option.*.modifier_option_price' => !empty($request['product_order_modifier_option']) ? 'required' : 'nullable',
        ]);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([
            Transaction::APP_TYPE_ID => AppTypeEnum::getShop(),
            Transaction::BUSINESS_ID => $request->input('shop_id'),
            Transaction::BUSINESS_OWNER_ID => $request->input('shop_owner_id'),
            Transaction::CODE => PrefixCode::getAutoCodeByBusiness(Transaction::TABLE_NAME, PrefixCode::TRANSACTION, $request->input('shop_id')),
        ]);

        //Set Data
        $transaction = new Transaction();
        $transaction->setData($request);
        $transaction->{Transaction::STATUS} = TransactionStatus::getPending();
        $transaction->{Transaction::CREATED_AT} = Carbon::now();

        if ($transaction->save()) {
            //Upload Transaction Image
            $image = StringHelper::uploadImage($request->input(Transaction::IMAGE), ImagePath::shopProductOrderTransaction);
            $transaction->{Transaction::IMAGE} = $image;
            $transaction->save();

            if(!empty($request->input('product_order_list'))) {
                foreach($request->input('product_order_list') as $obj) {
                    $product_order_list_data = [
                        ProductOrderList::TRANSACTION_ID => $transaction->{Transaction::ID},
                        ProductOrderList::PRODUCT_ID => $obj['product_id'],
                        ProductOrderList::QTY => $obj['qty'],
                        ProductOrderList::PRICE => $obj['price'],
                        ProductOrderList::CONCAT_MODIFIER => $obj['concat_modifier'],
                    ];

                    $product_order_list = new ProductOrderList();
                    $product_order_list->setData($product_order_list_data);
                    $product_order_list->created_at = Carbon::now();

                    if ($product_order_list->save()) {
                        // Calculate Total Price
                        $qty = floatval($obj['qty']);
                        $price = floatval($obj['price']);

                        $totalPrice = $qty * $price;
                        $product_order_list->total_price = $totalPrice;
                        $product_order_list->save();

                        if (!empty($obj['product_order_modifier_option'])) {
                            foreach ($obj['product_order_modifier_option'] as $modifier_option) {
                                $product_order_modifier_option_data = [
                                    ProductOrderModifierOption::PRODUCT_ORDER_LIST_ID => $product_order_list->{ProductOrderList::ID},
                                    ProductOrderModifierOption::PRODUCT_ID => $product_order_list->{ProductOrderList::PRODUCT_ID},
                                    ProductOrderModifierOption::MODIFIER_ID => $modifier_option['modifier_id'],
                                    ProductOrderModifierOption::MODIFIER_NAME => $modifier_option['modifier_name'],
                                    ProductOrderModifierOption::MODIFIER_NAME => $modifier_option['modifier_option_id'],
                                    ProductOrderModifierOption::MODIFIER_OPTION_ID => $modifier_option['modifier_option_id'],
                                    ProductOrderModifierOption::MODIFIER_OPTION_NAME => $modifier_option['modifier_option_name'],
                                    ProductOrderModifierOption::MODIFIER_OPTION_PRICE => $modifier_option['modifier_option_price']
                                ];
                                $product_order_modifier_option = new ProductOrderModifierOption();
                                $product_order_modifier_option->setData($product_order_modifier_option_data);
                                $product_order_modifier_option->save();
                            }
                        }
                    }
                }
            }

            // Get Cart Delete
            $cart = Cart::where('contact_id', $transaction->{Transaction::CUSTOMER_ID})
            ->where('business_type_id', $transaction->{Transaction::BUSINESS_TYPE_ID})
            ->where('business_id', $transaction->{Transaction::BUSINESS_ID})
            ->get();

            foreach ($cart as $cartItem) {
                // Delete Cart Modifier List
                CartModifier::where(CartModifier::CART_ID, $cartItem->id)->delete();
            }
            // Finally, delete the cart items
            $cart->each->delete();

            //Send notification
            $notificationData = [
                'business_type_id' => $request->input('business_type_id'),
                'business_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'customer_name' => Contact::find($transaction->{Transaction::CUSTOMER_ID})->fullname,
                'image' => Business::find($transaction->{Transaction::BUSINESS_ID})->image,
            ];
            //For Shop Owner
            $sendResponse = Notification::shopNotification(
                ContactNotificationType::getProductOrder(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Shop Booking: ' . $sendResponse);

            //For User Share
            $userShare = BusinessShareContact::getContactSharePermission($transaction->{Transaction::BUSINESS_ID}, BusinessPermission::VIEW_SALE_LIST_SHOP);

            if (!empty($userShare)) {
                foreach ($userShare as $obj) {
                    $sendResponse = Notification::shopNotification(
                        ContactNotificationType::getBookingShopBusinessForUserShare(),
                        $obj['contact_id'],
                        $transaction->{Transaction::ID},
                        $notificationData
                    );
                }
                info('Mobile Notification Shop Booking: ' . $sendResponse);
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Sale List Shop
    public function getSaleListShop(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_owner_id' => !empty($request->input('business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.customer_id' => !empty($request->input('customer_id')) ? 'required|exists:contact,id' : 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Transaction::listShop($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Sale Detail Shop
    public function getSaleDetailShop(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.sale_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listShop($filter)->first();

        return $this->responseWithData($data);
    }

    //Change Status Sale Shop
    public function changeStatusSaleShop(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'sale_id' => 'required|exists:transaction,id',
            'status' => 'required|numeric|min:2|max:5',
//            'remark' => $request->input('status') == TransactionStatus::getRejected() ? 'required' : 'nullable'
        ]);

        DB::beginTransaction();

        //Current Request Sale Data
        $saleID = $request->input('sale_id');
        $statusRequest = $request->input(Transaction::STATUS);

        //Get Old Sale Data
        $transaction = Transaction::find($saleID);
        $oldStatusInDB = $transaction->{Transaction::STATUS};

        //Check validation status
        if ($oldStatusInDB == TransactionStatus::getPending()) {
            //Only Status Pending
            if ($statusRequest != TransactionStatus::getApproved() &&
                $statusRequest != TransactionStatus::getRejected() &&
                $statusRequest != TransactionStatus::getCancelled()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        //Get Product in Order
        $product = [];
        $product = ProductOrderList::where('transaction_id', $saleID)->get();

        //Change to current status
        $transaction->{Transaction::STATUS} = $statusRequest;
        if ($transaction->save()) {
            //Send notification
            $contactNotiType = 0;
            $contactIDForNotification = $transaction->{Transaction::CUSTOMER_ID};
            $notificationData = [
                'business_type_id' => $transaction->{Transaction::BUSINESS_TYPE_ID},
                'business_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'code' => $transaction->{Transaction::CODE},
                'customer_name' => Contact::find($transaction->{Transaction::CUSTOMER_ID})->fullname,
                'image' => Business::find($transaction->{Transaction::BUSINESS_ID})->image,
            ];

            if ($transaction->{Transaction::STATUS} == TransactionStatus::getApproved()) {
                $contactNotiType = ContactNotificationType::getProductOrderApproved();

                // Adjust Stock Product
                if (!empty($product)) {
                    foreach ($product as $data) {
                        $product_data = Product::where('id', $data['product_id'])
                            ->first();

                        $qty = $product_data->qty - $data['qty'];

                        DB::table('product')
                        ->where('id', $data['product_id'])
                        ->update([
                            'qty' => $qty,
                            'updated_at' => Carbon::now()
                        ]);

                    }
                }

                //Transaction Fee and Amount
                $transactionFee = Business::getAppFeeByBusinessID($transaction->{Transaction::BUSINESS_ID});
                $transactionFeeAmount = StringHelper::getTransactionFeeAmount($transaction->{Transaction::SELL_AMOUNT}, $transactionFee);

                // Set Transaction Fee
                $transaction->{Transaction::TRANSACTION_FEE} = $transactionFee;
                $transaction->{Transaction::TRANSACTION_FEE_AMOUNT} = $transactionFeeAmount;
                $transaction->save();

            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejected()) {
                $contactNotiType = ContactNotificationType::getProductOrderRejected();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getCancelled()) {
                $contactNotiType = ContactNotificationType::getProductOrderCancelled();
                $contactIDForNotification = $transaction->{Transaction::BUSINESS_OWNER_ID};

                //For User Share
                $userShare = BusinessShareContact::getContactSharePermission($transaction->{Transaction::BUSINESS_ID}, BusinessPermission::VIEW_SALE_LIST_SHOP);

                if (!empty($userShare)) {
                    foreach ($userShare as $obj) {
                        $sendResponse = Notification::shopNotification(
                            ContactNotificationType::getCancelShopBusinessForUserShare(),
                            $obj['contact_id'],
                            $transaction->{Transaction::ID},
                            $notificationData
                        );
                    }
                    info('Mobile Notification Shop Booking: ' . $sendResponse);
                }
            }

            $sendResponse = Notification::shopNotification(
                $contactNotiType,
                $contactIDForNotification,
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Change Status Shop Product Order: ' . $sendResponse);
        }

        DB::commit();

        return $this->responseWithSuccess();
    }

}

<?php

namespace App\Http\Controllers\Mobile\Modules\Attraction;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\PrefixCode;
use App\Models\Transaction;
use App\Models\BusinessType;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\GeneralSetting;
use App\Enums\Types\AppTypeEnum;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Models\ProductOrderList;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\TransactionStatus;
use App\Enums\Types\ContactNotificationType;

class AttractionBookingAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function bookingAttractionPlace(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
            'business_owner_id' => 'required|exists:contact,id',
            'customer_id' => 'required',
            'check_in_date' => 'required',
            'sell_amount' => 'required',
            'bank_account_id' => 'required',
            'transaction_date' => 'required',
            'total_amount' => 'required',
            'image' => 'required',
            //invoice Detail
            'fullname' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'remark_booking' => 'nullable',
            //product_order_list
            'product_order_list' => 'required',
            'product_order_list.*.product_id' => 'required',
            'product_order_list.*.qty' => 'required',
            'product_order_list.*.price' => 'required',
        ]);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([
            Transaction::APP_TYPE_ID => AppTypeEnum::getAttraction(),
            Transaction::BUSINESS_OWNER_ID => $request->input('business_owner_id'),
            Transaction::BUSINESS_TYPE_ID => BusinessTypeEnum::getAttraction(),
            Transaction::CODE => PrefixCode::getAutoCodeByBusiness(Transaction::TABLE_NAME, PrefixCode::TRANSACTION, $request->input('business_id')),
        ]);

        $transaction = new Transaction();
        $transaction->setData($request);
        $transaction->{Transaction::STATUS} = TransactionStatus::getPending();
        $transaction->created_at = Carbon::now();

        if($transaction->save()) {
            //Upload Transaction Image
            $image = StringHelper::uploadImage($request->input(Transaction::IMAGE), ImagePath::attractionTransaction);
            $transaction->{Transaction::IMAGE} = $image;
            $transaction->save();

            //Set Product Order List
            if(!empty($request->input('product_order_list'))) {
                foreach($request->input('product_order_list') as $obj) {
                    $product_order_list_data = [
                        ProductOrderList::TRANSACTION_ID => $transaction->{Transaction::ID},
                        ProductOrderList::PRODUCT_ID => $obj[ProductOrderList::PRODUCT_ID],
                        ProductOrderList::QTY => $obj[ProductOrderList::QTY],
                        ProductOrderList::PRICE => $obj[ProductOrderList::PRICE],
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
                    }
                }
            }

            //Send notification
            $notificationData = [
                'business_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'business_image' => Business::find($transaction->{Transaction::BUSINESS_ID})->image,
                'customer_name' => Contact::find($transaction->{Transaction::CUSTOMER_ID})->fullname,
            ];

            $sendResponse = Notification::attractionNotification(
                ContactNotificationType::getAttractionBooking(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Attraction Booking' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Get Sale List Attraction
    public function getSaleListAttraction(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_owner_id' => !empty($request->input('business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.customer_id' => !empty($request->input('customer_id')) ? 'required|exists:contact,id' : 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Transaction::listAttraction($filter)
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Detail List Attraction
    public function getSaleListAttractionDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.sale_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listAttraction($filter)->first();

        return $this->responseWithData($data);
    }

    //Change Status Sale List
    public function changeStatusSaleAttraction(Request $request)
    {
        $this->validate($request, [
            'sale_id' => 'required|exists:transaction,id',
            'status' => 'required|numeric|min:2|max:5',
            'remark' => $request->input('status') == TransactionStatus::getRejected()
                ? 'required' : 'nullable'
        ]);

        DB::beginTransaction();

        //Current Request Sale Data
        $saleID = $request->input('sale_id');
        $statusRequest = $request->input(Transaction::STATUS);

        //Get Old Sale Data
        $transaction = Transaction::find($saleID);
        $oldStatusInDB = $transaction->{Transaction::STATUS};

        //Get Sale With Attraction
        $filter['sale_id'] = $saleID;
        $saleAttractionData = Transaction::listAttraction($filter)->first();

        //Check Validation Status
        if ($oldStatusInDB == TransactionStatus::getPending()) {
            //Only Status Pending
            if (
                $statusRequest != TransactionStatus::getApproved() &&
                $statusRequest != TransactionStatus::getCancelled() &&
                $statusRequest != TransactionStatus::getRejected()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }  else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        //Change to current Status
        $transaction->{Transaction::STATUS} = $statusRequest;
        if($transaction->save()) {
            $contactNotiType = 0;
            $contactIDForNotification = $transaction->{Transaction::CUSTOMER_ID};

            //Set Attraction Status by Request Change  Status
            if ($transaction->{Transaction::STATUS} == TransactionStatus::getApproved()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAttractionApprove();

                //Transaction Fee and Amount
                $transactionFee = Business::getAppFeeByBusinessID($transaction->{Transaction::BUSINESS_ID});
                $transactionFeeAmount = StringHelper::getTransactionFeeAmount($transaction->{Transaction::SELL_AMOUNT}, $transactionFee);

                // Set Transaction Fee
                $transaction->{Transaction::TRANSACTION_FEE} = $transactionFee;
                $transaction->{Transaction::TRANSACTION_FEE_AMOUNT} = $transactionFeeAmount;
                $transaction->save();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejected()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification data
                $contactNotiType = ContactNotificationType::getAttractionReject();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getCancelled()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAttractionCancel();
                $contactIDForNotification = $transaction->{Transaction::BUSINESS_OWNER_ID};
            }

            //Send notification
            $sendResponse = Notification::attractionNotification(
                $contactNotiType,
                $contactIDForNotification,
                $transaction->{Transaction::ID},
                $saleAttractionData
            );
            info('Mobile Notification Change Status Sale Attraction: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

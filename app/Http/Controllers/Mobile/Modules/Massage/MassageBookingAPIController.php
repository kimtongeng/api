<?php

namespace App\Http\Controllers\Mobile\Modules\Massage;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\PrefixCode;
use App\Models\Transaction;
use App\Models\BusinessType;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\BusinessStaff;
use App\Models\GeneralSetting;
use App\Enums\Types\AppTypeEnum;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Models\ProductOrderList;
use App\Models\TransactionContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\TransactionActive;
use App\Enums\Types\TransactionStatus;
use App\Models\TransactionContactDetail;
use App\Enums\Types\ContactNotificationType;

class MassageBookingAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function checkValidation($data)
    {
        $this->validate($data, [
            'business_id' => 'required|exists:business,id',
            'business_owner_id' => 'required|exists:contact,id',
            'customer_id' => 'required',
            'order_type' => 'required',
            'shipping_address_id' => 'nullable',
            'booking_date' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'sell_amount' => 'nullable',
            'transaction_contact' => 'required',
            'transaction_contact.*.contact_id' => 'required',
            'transaction_contact.*.tip_amount' => !empty($data['tip_amount']) ? 'required' : 'nullable',
            //delete_transaction_contact
            // 'deleted_transaction_contact.*.id' => !empty($data['id']) && !empty($data['deleted_transaction_contact']) ? 'required|exists:transaction_contact.id' : 'nullable',
            //Time Slots
            'transaction_contact_detail' => 'required',
            'transaction_contact_detail.*.contact_id' => 'required',
            'transaction_contact_detail.*.start_time' => 'required',
            'transaction_contact_detail.*.end_time' => 'required',
            //Product
            'product_order_list' => 'required',
            'product_order_list.*.product_id' => 'required',
            //Invoice Detail
            'fullname' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'remark_booking' => 'nullable',
        ]);
    }

    /**
     * Check Filter Time Massage Therapist
     */
    public function checkMassagerFilterTime(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
            'book_date' => 'required',
            'duration' => 'required',
            'massager' => 'required',
            'massager.*.contact_id' => 'required',
        ]);

        $businessId = $request->input('business_id');
        $bookDate = Carbon::createFromFormat('Y-m-d H:i:s.u',$request->input('book_date'));
        $checkInDate = $bookDate->format('Y-m-d');
        $duration = $request->input('duration');

        $data = [];
        foreach($request->input('massager') as $obj) {
            $data[] = BusinessStaff::listMassageTherapist()
            ->where('business_staff.business_id', $businessId)
            ->where('business_staff.contact_id', $obj['contact_id'])
            ->whereNull('business_staff.deleted_at')
            ->first();
        }

        $contact_massager = Transaction::select(
            'contact.fullname',
            'transaction_contact_detail.id',
            'transaction_contact_detail.start_time',
            'transaction_contact_detail.end_time',
            'transaction.check_in_date'
        )
        ->join('transaction_contact','transaction.id', 'transaction_contact.transaction_id')
        ->join('transaction_contact_detail', function ($join) {
            $join->on('transaction_contact_detail.contact_id', '=', 'transaction_contact.contact_id')
                ->on('transaction_contact_detail.transaction_id', '=', 'transaction_contact.transaction_id');
        })
        ->join('contact','contact.id','transaction_contact.contact_id')
        ->where(DB::raw('date(transaction.check_in_date)'), $checkInDate)
        ->where('transaction.business_id',$businessId)
        ->whereIn('transaction_contact.contact_id', $request->input('massager'))
        ->groupBy('transaction_contact_detail.id')
        ->get();

        $time_slots = [];
        foreach($data as $obj) {
            $business_staff = new BusinessStaff();
            $time_slots = $business_staff->generateTimeSlots($obj['work_start_time'],$obj['work_end_time'], $obj['contact_id'],$duration, $contact_massager);
            $dataDetail[] = [
                "id" => $obj['id'],
                "name" => $obj['name'],
                "code" => $obj['code'],
                "time_slot" => $time_slots
            ];
        }

        return $this->responseWithData($dataDetail);
    }


    /**
     * Booking Massage service
     *
     */
    public function bookingMassageService(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([
            Transaction::APP_TYPE_ID => AppTypeEnum::getMassage(),
            Transaction::BUSINESS_ID => $request->input('business_id'),
            Transaction::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
            Transaction::BUSINESS_OWNER_ID => $request->input('business_owner_id'),
            Transaction::CODE => PrefixCode::getAutoCodeByBusiness(Transaction::TABLE_NAME, PrefixCode::TRANSACTION, $request->input('business_id')),
        ]);

        $transaction = new Transaction();
        $transaction->setData($request);
        $transaction->status = TransactionStatus::getPending();
        $transaction->active = TransactionActive::getEnable();

        if($transaction->save()) {
            //Set Massage Therapist
            if(!empty($request->input('transaction_contact'))) {
                foreach($request->input('transaction_contact') as $obj) {
                    $transaction_contact_data = [
                        TransactionContact::TRANSACTION_ID => $transaction->{Transaction::ID},
                        TransactionContact::BUSINESS_ID => $transaction->{Transaction::BUSINESS_ID},
                        TransactionContact::CONTACT_ID => $obj['contact_id'],
                        TransactionContact::TIP_AMOUNT => $obj['tip_amount'],
                    ];
                    $transaction_contact = new TransactionContact();
                    $transaction_contact->setData($transaction_contact_data);
                    $transaction_contact->save();
                }
            }

            //Set Product Order
            if (!empty($request->input('product_order_list'))) {
                foreach($request->input('product_order_list') as $obj) {
                    $product_order_list_data = [
                        ProductOrderList::TRANSACTION_ID => $transaction->{Transaction::ID},
                        ProductOrderList::PRODUCT_ID => $obj['product_id'],
                        ProductOrderList::QTY => $transaction->{Transaction::QTY},
                        ProductOrderList::PRICE => $request->input('price')
                    ];
                    $product_order_list = new ProductOrderList();
                    $product_order_list->setData($product_order_list_data);
                    $product_order_list->created_at = Carbon::now();

                    if ($product_order_list->save()) {
                        // Calculate Total Price
                        $qty = floatval($product_order_list->{ProductOrderList::QTY});
                        $price = floatval($product_order_list->{ProductOrderList::PRICE});

                        $totalPrice = $qty * $price;
                        $product_order_list->total_price = $totalPrice;
                        $product_order_list->save();
                    }
                }
            }

            //Set Transaction Contact Detail
            if(!empty($request->input('transaction_contact_detail'))) {
                foreach($request->input('transaction_contact_detail') as $obj) {
                    $transaction_contact_detail_data = [
                        TransactionContactDetail::TRANSACTION_ID => $transaction->{Transaction::ID},
                        TransactionContactDetail::BUSINESS_ID => $transaction->{Transaction::BUSINESS_ID},
                        TransactionContactDetail::CONTACT_ID => $obj['contact_id']
                    ];
                    $transaction_contact_detail = new TransactionContactDetail();
                    $transaction_contact_detail->setData($transaction_contact_detail_data);
                    if($transaction_contact_detail->save()) {
                        $startTime = Carbon::parse($obj['start_time'])->format('H:i:s');
                        $endTime = Carbon::parse($obj['end_time'])->format('H:i:s');

                        $transaction_contact_detail->start_time = $startTime;
                        $transaction_contact_detail->end_time = $endTime;
                        $transaction_contact_detail->save();
                    }
                }
            }

            //Set Check In Date & Check Out Date
            $bookingDate = Carbon::createFromFormat('Y-m-d H:i:s.u', trim($request->input('booking_date')));
            $checkInDateTime = Carbon::parse($bookingDate->format('Y-m-d'));

            $transaction->check_in_date = $checkInDateTime;
            $transaction->save();

            //Calculate booking Service
            $totalTipAmount = 0;
            $qty = $request->input('qty');
            $price = $request->input('price');
            $transactionContact = TransactionContact::join('transaction', 'transaction.id', 'transaction_contact.transaction_id')
                ->where('transaction_contact.transaction_id', $transaction->{Transaction::ID})
                ->get();
            $tipAmount = TransactionContact::where('transaction_contact.transaction_id', '=', $transaction->{Transaction::ID})
            ->select(DB::raw('SUM(transaction_contact.tip_amount) as total_tip_amount'))
            ->first();
            $countTransactionContact = $transactionContact->count();
            $sellPrice = $qty * $price * $countTransactionContact;
            $totalTipAmount = $tipAmount->total_tip_amount;

            $transaction->{Transaction::SELL_AMOUNT} = $sellPrice;
            $transaction->{Transaction::TOTAL_TIP_AMOUNT} = $totalTipAmount;
            $transaction->save();

            //Send Notification Data
            $notificationData = [
                'business_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'business_image' =>Business::find($transaction->{Transaction::BUSINESS_ID})->image,
                'customer_name' => Contact::find($transaction->{Transaction::CUSTOMER_ID})->fullname,
            ];
            $sendResponse = Notification::massageShopNotification(
                ContactNotificationType::getMassageShopBooking(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Massage Booking: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Get Sale List Massage
     *
     */
    public function getSaleListMassage(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_owner_id' => !empty($request->input('business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.customer_id' => !empty($request->input('customer_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.massager_id' => !empty($request->input('massager_id')) ? 'required' : 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Transaction::listMassage($filter,$sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     *Get Sale List Massage Detail
     *
     */
    public function getSaleListMassageDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.sale_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listMassage($filter)->first();

        return $this->responseWithData($data);
    }

    /**
     * Change Status Sale List Massage
     *
     */
    public function changeStatusSaleListMassage(Request $request)
    {
        $this->validate($request, [
            'sale_id' => 'required|exists:transaction,id',
            'status' => 'required|numeric|min:2|max:8',
            'type' => 'required',
            'remark' => $request->input('status') == TransactionStatus::getRejected() ||
                $request->input('status') == TransactionStatus::getRejectPayment()
                ? 'required' : 'nullable'
        ]);


        DB::beginTransaction();

        //Current Request Sale Data
        $saleID = $request->input('sale_id');
        $statusRequest = $request->input(Transaction::STATUS);

        //Get Old Sale Data
        $transaction = Transaction::find($saleID);
        $oldStatusInDB = $transaction->{Transaction::STATUS};

        //Get Sale With Massage
        $filter['sale_id'] = $saleID;
        $saleMassageData = Transaction::listMassage($filter)->first();

        //Get Transaction Contact
        $transactionContact = TransactionContact::join('transaction', 'transaction.id', 'transaction_contact.transaction_id')
        ->where('transaction_contact.transaction_id', $saleID)
        ->get();

        //Check Validation Status
        if($oldStatusInDB == TransactionStatus::getPending()) {
            //Only Status Pending
            if($statusRequest != TransactionStatus::getPendingPayment() &&
            $statusRequest != TransactionStatus::getRejected() &&
            $statusRequest != TransactionStatus::getCancelled()) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else if ($oldStatusInDB == TransactionStatus::getPendingPayment()) {
            //Only Status Pending Payment
            if (
                $statusRequest != TransactionStatus::getCancelled() &&
                $statusRequest != TransactionStatus::getRejected()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else if ($oldStatusInDB == TransactionStatus::getAuditingPayment()) {
            //Only Status Auditing Payment
            if (
                $statusRequest != TransactionStatus::getApproved() &&
                $statusRequest != TransactionStatus::getRejectPayment()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        //Change to Current Status
        $transaction->{Transaction::STATUS} = $statusRequest;
        if($transaction->save()) {
            $contactNotiType = 0;

            if ($transaction->{Transaction::CUSTOMER_ID} == $request->input('type')) {
                $contactIDForNotification = $transaction->{Transaction::CUSTOMER_ID};
            } else if ($transaction->{Transaction::BUSINESS_OWNER_ID} == $request->input('type')) {
                $contactIDForNotification = $transaction->{Transaction::BUSINESS_OWNER_ID};
            } else {
                $contactIDForNotification = null;
            }

            //Set Massage status by status request
            if($transaction->{Transaction::STATUS} == TransactionStatus::getApproved()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageShopApprove();

                if(!empty($transactionContact)) {
                    foreach($transactionContact as $obj) {
                        //Send Notification
                        $sendResponse = Notification::massageShopNotification(
                            ContactNotificationType::getMassageShopForMassager(),
                            $obj['contact_id'],
                            $transaction->{Transaction::ID},
                            $saleMassageData
                        );
                        info('Mobile Notification Change Status Sale Massage: ' . $sendResponse);
                    }
                }

                //Transaction Fee and Amount
                $transactionFee = Business::getAppFeeByBusinessID($transaction->{Transaction::BUSINESS_ID});
                $transactionFeeAmount = StringHelper::getTransactionFeeAmount($transaction->{Transaction::SELL_AMOUNT}, $transactionFee);

                // Set Transaction Fee
                $transaction->{Transaction::TRANSACTION_FEE} = $transactionFee;
                $transaction->{Transaction::TRANSACTION_FEE_AMOUNT} = $transactionFeeAmount;
                $transaction->save();
            } else if($transaction->{Transaction::STATUS} == TransactionStatus::getPendingPayment()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageShopPayment();
            } else if($transaction->{Transaction::STATUS} == TransactionStatus::getAuditingPayment()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageShopAuditingPayment();
            } else if($transaction->{Transaction::STATUS} == TransactionStatus::getRejected()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageShopReject();
            } else if($transaction->{Transaction::STATUS} == TransactionStatus::getCancelled()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageShopCancel();
            } else if($transaction->{Transaction::STATUS} == TransactionStatus::getAuditingPayment()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageShopAuditingPayment();
            } else if($transaction->{Transaction::STATUS} == TransactionStatus::getRejectPayment()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageShopRejectPayment();
            }

            //Send Notification
            $sendResponse = Notification::massageShopNotification(
                $contactNotiType,
                $contactIDForNotification,
                $transaction->{Transaction::ID},
                $saleMassageData
            );
            info('Mobile Notification Change Status Sale Massage: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    // Add Payment for Accommodation
    public function addPaymentMassageShop(Request $request)
    {
        $this->validate($request, [
            'sale_id' => 'required|exists:transaction,id',
            'bank_account_id' => 'required',
            'transaction_date' => 'required',
            'image' => 'required',
            'total_amount' => 'required',
        ]);

        DB::beginTransaction();

        //Current Request Sale Data
        $saleID = $request->input('sale_id');

        //Get Old Sale Data
        $transaction = Transaction::find($saleID);
        $statusInDB = $transaction->{Transaction::STATUS};

        //Get Sale With Accommodation
        $filter['sale_id'] = $saleID;
        $saleMassageData = Transaction::listMassage($filter)->first();

        // Check Status for add payment
        if ($statusInDB == TransactionStatus::getPendingPayment()) {
            $transactionPayment = Transaction::find($saleID);

            // Add More Data into Transaction
            $transactionPayment->bank_account_id = $request->input('bank_account_id');
            $transactionPayment->transaction_date = $request->input('transaction_date');
            $transactionPayment->total_amount = $request->input('total_amount');
            $transactionPayment->created_at = Carbon::now();
            $transactionPayment->updated_at = Carbon::now();
            $transactionPayment->save();

            // Save Data
            if ($transactionPayment->save()) {
                // Upload Transaction Image
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::massageTransaction);
                $transactionPayment->{Transaction::IMAGE} = $image;
                $transactionPayment->save();
            }

            // Change status
            $transactionPayment->{Transaction::STATUS} = TransactionStatus::getAuditingPayment();
            $transactionPayment->save();

            //Send notification
            $sendResponse = Notification::massageShopNotification(
                ContactNotificationType::getMassageShopAuditingPayment(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $saleMassageData
            );
            info('Mobile Notification Change Status Sale Massage: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

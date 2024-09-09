<?php

namespace App\Http\Controllers\Mobile\Modules\KTV;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Contact;
use App\Models\BookList;
use App\Models\Business;
use App\Models\PrefixCode;
use App\Models\Transaction;
use App\Models\BusinessType;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\BusinessStaff;
use App\Enums\Types\AppTypeEnum;
use App\Enums\Types\BusinessStaffStatus;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Models\ProductOrderList;
use App\Models\TransactionContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\TransactionStatus;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\RoomStatus;
use App\Enums\Types\TransactionActive;
use phpDocumentor\Reflection\Types\This;

class KTVBookingAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Check KTV Room
    public function checkKTVRoomFilterDate(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
            'check_in_date' => 'required',
        ]);

        // Request Data
        $businessId = $request->input('business_id');
        $check_in = $request->input('check_in_date');

        /** Check Business Open_time and Close_time */
        $checkBusiness = Business::select(
                'business.id',
                'business.name',
                DB::raw('TIME(business.open_time) as open_time'),
                DB::raw('TIME(business.close_time) as close_time'),
            )
            ->where('business.id', $businessId)
            ->whereRaw("CheckOpenTime(open_time, close_time, TIME('" . $check_in . "'))")
            ->first();

        if (empty($checkBusiness)) {
            $data = Business::where('id', $businessId)
            ->select('name', DB::raw('TIME(open_time) as open_time'),DB::raw('TIME(close_time) as close_time'),)
            ->first();

            if ($data) {
                return $this->responseValidation(ErrorCode::BUSINESS_IS_CLOSE);
            }
        }

        $data = Room::leftJoinSub(function ($query) use ($businessId) {
            $query->from('transaction')
            ->select(
                'transaction.id',
                'transaction.transaction_date',
                'transaction.check_in_date',
                'transaction.status',
                'book_list.room_id'
            )
            ->join('book_list', 'transaction.id', '=', 'book_list.transaction_id')
            ->where('transaction.business_id', $businessId)
            ->whereIn('transaction.id', function ($subQuery) use ($businessId) {
                $subQuery->select(DB::raw('MAX(t2.id)'))
                ->from('transaction as t2')
                ->join('book_list as bl2', 't2.id', '=', 'bl2.transaction_id')
                ->where('t2.business_id', $businessId)
                    ->groupBy('bl2.room_id');
            });
        }, 'b', 'room.id', '=', 'b.room_id')
        ->select(
            'room.id',
            'room.business_id',
            'room.name',
            'room.code',
            'room.image',
            'room.total_price',
            DB::raw("
            CASE
                WHEN DATE(b.transaction_date) = DATE('{$check_in}')
                AND b.status = '" . TransactionStatus::getApproved() . "' THEN 0
                ELSE 1
            END as book_status
        ")
        )
        ->where('room.business_id', $businessId)
        ->where('room.status', RoomStatus::getEnable())
        ->orderBy('b.id', 'desc')
        ->get();

        return $this->responseWithData($data);
    }


    //Check KTV Girl
    public function checkKTVGirlFilter(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required',
            'check_in_date' => 'required',
        ]);

        // Request Data
        $businessId = $request->input('business_id');
        $check_in = $request->input('check_in_date');

        $data = BusinessStaff::join('contact', 'contact.id', '=', 'business_staff.contact_id')
        ->join('business_staff_workdays', function ($join) {
            $join->on('business_staff.business_id', '=', 'business_staff_workdays.business_id')
                ->on('business_staff.contact_id', '=', 'business_staff_workdays.contact_id');
        })
        ->leftJoin('contact_business_info', function ($join) {
            $join->on('contact_business_info.contact_id', '=', 'contact.id')
            ->where('contact_business_info.business_type_id', '=', BusinessTypeEnum::getKtv());
        })
            ->leftJoinSub(function ($query) use ($businessId) {
                $query->from('transaction')
                ->select(
                    'transaction.id',
                    'transaction.transaction_date',
                    'transaction.status',
                    'transaction_contact.contact_id'
                )
                    ->join('transaction_contact', 'transaction.id', '=', 'transaction_contact.transaction_id')
                    ->where('transaction.business_id', $businessId)
                    ->whereIn('transaction.id', function ($subQuery) {
                        $subQuery->select(DB::raw('MAX(t2.id)'))
                        ->from('transaction as t2')
                        ->join('transaction_contact as tc2', 't2.id', '=', 'tc2.transaction_id')
                        ->groupBy('tc2.contact_id');
                    });
            }, 'c', 'contact.id', '=', 'c.contact_id')
            ->select(
                'business_staff.id',
                'business_staff.contact_id',
                'contact.fullname as name',
                'contact_business_info.image',
                'business_staff.code',
                'business_staff.price',
                DB::raw("
                    CASE
                        WHEN DATE(c.transaction_date) = DATE('{$check_in}')
                        AND c.status = '" . TransactionStatus::getApproved() . "' THEN 0
                        WHEN INSTR(GROUP_CONCAT(business_staff_workdays.day), WEEKDAY('{$check_in}')) > 0
                        AND CheckOpenTime(TIME(business_staff.work_start_time), TIME(business_staff.work_end_time), TIME('{$check_in}')) THEN 1
                        ELSE 2
                    END as book_status
                ")
            )
            ->where('business_staff.business_id', $businessId)
            ->where('business_staff.status', BusinessStaffStatus::getEnable())
            ->groupBy('contact.id')
            ->orderBy('c.id', 'desc')
            ->get();

        return $this->responseWithData($data);
    }


    public function checkValidation($data)
    {
        $this->validate($data, [
            'business_id' => 'required|exists:business,id',
            'business_owner_id' => 'required|exists:contact,id',
            'customer_id' => 'required',
            'booking_date' => 'required',
            'sell_amount' => 'required',
            //KTV Room
            'book_room_list' => 'required',
            'book_room_list.*.room_id' => 'required|exists:room,id',
            'book_room_list.*.price' => 'required',
            //KTV Girl
            'transaction_contact' => 'required',
            'transaction_contact.*.contact_id' => 'required',
            'transaction_contact.*.price' => 'required',
            //KTV Product
            'product_order_list' => 'required',
            'product_order_list.*.product_id' => 'required',
            'product_order_list.*.qty' => 'required',
            'product_order_list.*.price' => 'required',
            //Invoice Detail
            'fullname' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'remark_booking' => 'nullable',
        ]);
    }

    //Booking KTV
    public function bookingKTV(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([
            Transaction::APP_TYPE_ID => AppTypeEnum::getKtv(),
            Transaction::BUSINESS_ID => $request->input('business_id'),
            Transaction::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
            Transaction::BUSINESS_OWNER_ID => $request->input('business_owner_id'),
            Transaction::CODE => PrefixCode::getAutoCodeByBusiness(Transaction::TABLE_NAME, PrefixCode::TRANSACTION, $request->input('business_id')),
        ]);

        $transaction = new Transaction();
        $transaction->setData($request);
        $transaction->status = TransactionStatus::getPending();
        $transaction->active = TransactionActive::getEnable();

        if($transaction->save()) {
            // Booking KTV Room
            if (!empty($request->input('book_room_list'))) {
                foreach ($request->input('book_room_list') as $obj) {
                    // Set Book List
                    $book_list_data = [
                        BookList::TRANSACTION_ID => $transaction->{Transaction::ID},
                        BookList::ROOM_ID => $obj['room_id'],
                        BookList::PRICE => $obj['price'],
                    ];
                    $book_list = new BookList();
                    $book_list->setData($book_list_data);
                    $book_list->created_at = Carbon::now();
                    $book_list->save();
                }
            }

            //Booking Product KTV
            if (!empty($request->input('product_order_list'))) {
                foreach ($request->input('product_order_list') as $obj) {
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

            //Booking KTV Girl
            if (!empty($request->input('transaction_contact'))) {
                foreach ($request->input('transaction_contact') as $obj) {
                    $transaction_contact_data = [
                        TransactionContact::TRANSACTION_ID => $transaction->{Transaction::ID},
                        TransactionContact::BUSINESS_ID => $transaction->{Transaction::BUSINESS_ID},
                        TransactionContact::CONTACT_ID => $obj['contact_id'],
                        TransactionContact::PRICE => $obj['price'],
                    ];
                    $transaction_contact = new TransactionContact();
                    $transaction_contact->setData($transaction_contact_data);
                    $transaction_contact->save();
                }
            }

            //Set Check In Date
            $bookingDate = Carbon::createFromFormat('Y-m-d H:i:s.u', trim($request->input('booking_date')));
            $checkInDateTime = Carbon::parse($bookingDate->format('Y-m-d H:i:s'));

            $transaction->{Transaction::CHECK_IN_DATE} = $checkInDateTime;
            $transaction->save();

            //Send Notification Data
            $notificationData = [
                'business_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'business_image' => Business::find($transaction->{Transaction::BUSINESS_ID})->image,
                'customer_name' => Contact::find($transaction->{Transaction::CUSTOMER_ID})->fullname,
            ];

            $sendResponse = Notification::ktvShopNotification(
                ContactNotificationType::getKtvBooking(),
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

    public function getSaleListKTV(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_owner_id' => !empty($request->input('business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.customer_id' => !empty($request->input('customer_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.ktv_girl_id' => !empty($request->input('ktv_girl_id')) ? 'required' : 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Transaction::listKTV($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    public function getSaleKTVDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.sale_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listKTV($filter)->first();

        return $this->responseWithData($data);
    }

    public function changeStatusSaleKTV(Request $request)
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
        $saleKTVData = Transaction::listKTV($filter)->first();

        //Get Booking Room
        $bookingRoom = BookList::join('transaction', 'transaction.id', 'book_list.transaction_id')
        ->where('book_list.transaction_id', $saleID)
        ->get();

        //Get Transaction Contact
        $transactionContact = TransactionContact::join('transaction', 'transaction.id', 'transaction_contact.transaction_id')
        ->where('transaction_contact.transaction_id', $saleID)
        ->get();

        //Check Validation Status
        if ($oldStatusInDB == TransactionStatus::getPending()) {
            //Only Status Pending
            if (
                $statusRequest != TransactionStatus::getPendingPayment() &&
                $statusRequest != TransactionStatus::getRejected() &&
                $statusRequest != TransactionStatus::getCancelled()
            ) {
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
        } else if ($oldStatusInDB == TransactionStatus::getApproved()) {
            //Only Status Approve
            if (
                $statusRequest != TransactionStatus::getCompleted()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        //Change to Current Status
        $transaction->{Transaction::STATUS} = $statusRequest;
        if ($transaction->save()) {
            $contactNotiType = 0;

            if ($transaction->{Transaction::CUSTOMER_ID} == $request->input('type')) {
                $contactIDForNotification = $transaction->{Transaction::BUSINESS_OWNER_ID};
            } else if ($transaction->{Transaction::BUSINESS_OWNER_ID} == $request->input('type')) {
                $contactIDForNotification = $transaction->{Transaction::CUSTOMER_ID};
            } else {
                $contactIDForNotification = null;
            }

            //Set Massage status by status request
            if ($transaction->{Transaction::STATUS} == TransactionStatus::getApproved()) {

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvApprove();

                if (!empty($transactionContact)) {
                    foreach ($transactionContact as $obj) {
                        //Send Notification
                        $sendResponse = Notification::ktvShopNotification(
                            ContactNotificationType::getKtvForKtvGirl(),
                            $obj['contact_id'],
                            $transaction->{Transaction::ID},
                            $saleKTVData
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
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getPendingPayment()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvAddPayment();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getAuditingPayment()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvAuditingPayment();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejected()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvReject();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getCancelled()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvCancel();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejectPayment()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvRejectPayment();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getCompleted()) {

                //Set Check Out Time
                $update = DB::table('transaction')
                    ->where('id', $transaction->{Transaction::ID})
                    ->update([
                        'check_out_date' => Carbon::now()
                    ]);

                if ($update) {
                    return $this->responseWithSuccess();
                }
            }

            //Send Notification
            $sendResponse = Notification::ktvShopNotification(
                $contactNotiType,
                $contactIDForNotification,
                $transaction->{Transaction::ID},
                $saleKTVData
            );
            info('Mobile Notification Change Status Sale Massage: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    public function addPaymentKTVShop(Request $request)
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
        $saleKTVData = Transaction::listKTV($filter)->first();

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
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::ktvTransaction);
                $transactionPayment->{Transaction::IMAGE} = $image;
                $transactionPayment->save();
            }

            // Change status
            $transactionPayment->{Transaction::STATUS} = TransactionStatus::getAuditingPayment();
            $transactionPayment->save();

            //Send notification
            $sendResponse = Notification::ktvShopNotification(
                ContactNotificationType::getKtvAuditingPayment(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $saleKTVData
            );
            info('Mobile Notification Change Status Sale Massage: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Change Active For Customer KTV List To Disable
    public function changeActiveKTVCustomerSaleList(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'sale_id' => 'required|exists:transaction,id',
            'active' => 'required|numeric'
        ]);

        DB::beginTransaction();

        $transaction = Transaction::find($request->input('sale_id'));
        $transaction->active = $request->input('active');
        $transaction->save();

        DB::commit();
        return $this->responseWithSuccess();
    }
}

<?php

namespace App\Http\Controllers\Mobile\Modules\Accommodation;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\BookList;
use App\Models\Business;
use App\Models\RoomType;
use App\Models\PrefixCode;
use App\Models\Transaction;
use App\Models\BusinessType;
use App\Models\GalleryPhoto;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\GeneralSetting;
use App\Enums\Types\RoomStatus;
use App\Enums\Types\AppTypeEnum;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\TransactionStatus;
use App\Enums\Types\NotificationSendType;
use App\Enums\Types\ContactNotificationType;

class AccommodationBookingAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Check Filter Room Booking
    public function checkRoomFilterDate(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
            'check_in_date' => 'required',
            'check_out_date' => 'required',
        ]);

        // Request Data
        $businessId = $request->input('business_id');
        $check_in = $request->input('check_in_date');
        $check_out = $request->input('check_out_date');
        $sortBy = $request->input('sort');
        $price = $request->input('price');

        //Price Range
        $priceMin = isset($price['min']) ? floatval($price['min']) : null;
        $priceMin = $priceMin == 0 ? '' : $priceMin;
        $priceMax = isset($price['max']) ? floatval($price['max']) : null;
        $priceMax = $priceMax == 0 ? '' : $priceMax;

        //Sort Mobile
        $priceAsc = $sortBy == 'price_asc' ? 'price_asc' : null;
        $priceDsc = $sortBy == 'price_desc' ? 'price_desc' : null;

        $data = RoomType::list()
            ->join('room', 'room.room_type_id', 'room_type.id')
            ->groupBy('room_type.id')
            ->where('room.status', RoomStatus::getEnable())
            ->where('room_type.business_id', $businessId)
            ->with([
                'roomList' => function ($query) use (
                    $check_in, $businessId, $priceMax, $priceMin, $priceAsc, $priceDsc)
                {
                    $query->leftjoin('room_type', 'room_type.id', 'room.room_type_id')
                    ->leftJoinSub(function ($query) use ($check_in) {
                        $query->from('transaction')
                            ->select(
                                'book_list.room_id',
                                'transaction.transaction_date',
                                'transaction.status',
                                'transaction.check_out_date'
                            )
                            ->distinct()
                            ->join('book_list', 'transaction.id', 'book_list.transaction_id')
                            ->where(DB::raw("date('transaction.check_out_date') >= '" . $check_in . "'"))
                            ->where('transaction.status', '=', TransactionStatus::getApproved());
                    }, 'e', 'room.id', '=', 'e.room_id')
                    ->select(
                        'room.id',
                        'room.business_id',
                        'room_type.id as room_type_id',
                        'room_type.name as room_type_name',
                        'room.name',
                        'room.image',
                        'room.total_price',
                        'e.transaction_date',
                        DB::raw(
                        "
                            CASE
                                WHEN e.transaction_date IS NULL THEN 1
                                WHEN e.transaction_date IS NOT NULL AND e.status = '" . TransactionStatus::getApproved() . "' AND e.check_out_date <= '" . $check_in . "' THEN 1
                                ELSE  0
                            END AS status
                        ")
                    )
                    ->when($priceMin, function ($query) use ($priceMin) {
                        $query->where('room.total_price', '>=', $priceMin);
                    })
                    ->when($priceMax, function ($query) use ($priceMax) {
                        $query->where('room.total_price', '<=', $priceMax);
                    })
                    ->when($priceAsc, function ($query) {
                        $query->orderBy("room.total_price", "ASC");
                    })
                    ->when($priceDsc, function ($query) {
                        $query->orderBy("room.total_price", "DESC");
                    })
                    ->with([
                        'galleryPhoto' => function ($query) {
                            $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getAccommodationRoom())
                            ->orderBy('gallery_photo.order', 'ASC');
                        }
                    ])
                    ->where('room.status', RoomStatus::getEnable())
                    ->where('room_type.business_id', $businessId)
                    ->orderBy('room_type.id')
                    ->groupBy('room.id')
                    ->get();
                }
            ])
            ->get();

        return $this->responseWithData($data);
    }

    //Accommodation Booking Room
    public function bookingAccommodationRoom(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id',
            'business_id' => 'required|exists:business,id',
            'business_owner_id' => 'required|exists:contact,id',
            'customer_id' => 'required',
            'check_in_date' => 'required',
            'check_out_date' => 'required',
            'qty' => 'required',
            //book room list
            'book_room_list' => 'required',
            'book_room_list.*.room_id' => 'required|exists:room,id',
            'book_room_list.*.price' => 'required',
            //invoice Detail
            'fullname' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'remark_booking' => 'nullable',
        ]);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([
            Transaction::APP_TYPE_ID => AppTypeEnum::getAccommodation(),
            Transaction::BUSINESS_OWNER_ID => $request->input('business_owner_id'),
            Transaction::CODE => PrefixCode::getAutoCodeByBusiness(Transaction::TABLE_NAME, PrefixCode::TRANSACTION, $request->input('business_id')),
        ]);

        $transaction = new Transaction();
        $transaction->setData($request);
        $transaction->{Transaction::STATUS} = TransactionStatus::getPending();
        $transaction->created_at = Carbon::now();

        if($transaction->save()) {
            if(!empty($request->input('book_room_list'))) {
                foreach($request->input('book_room_list') as $obj) {
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

            //Calculate Total Amount
            $startDate = Carbon::parse($request->input('check_in_date'));
            $endDate = Carbon::parse($request->input('check_out_date'));
            $dayCount = $startDate->diffInDays($endDate);

            $sumPrice = BookList::sumPrice($transaction->{Transaction::ID});
            $roomPrice = $sumPrice;
            $roomQty = $request->input('qty');

            $totalRoomPrice = $roomPrice;
            $sellAmount = $totalRoomPrice * $dayCount;

            $transaction->qty = $roomQty;
            $transaction->sell_amount = $sellAmount;
            $transaction->save();

            //Send notification
            $notificationData = [
                'accommodation_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'accommodation_image' => Business::find($transaction->{Transaction::BUSINESS_ID})->image,
                'customer_name' => Contact::find($transaction->{Transaction::CUSTOMER_ID})->fullname,
            ];
            $sendResponse = Notification::accommodationNotification(
                ContactNotificationType::getAccommodationBooking(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Accommodation Booking: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    // Get Booking List Room
    public function getSaleListAccommodation(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_owner_id' => !empty($request->input('business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.customer_id' => !empty($request->input('customer_id')) ? 'required|exists:contact,id' : 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Transaction::listAccommodationRoom($filter,$sort)->paginate($tableSize);

        return $this->responseWithPagination($data);

    }

    // Sale Detail
    public function getSaleAccommodationDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.sale_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listAccommodationRoom($filter)->first();

        return $this->responseWithData($data);
    }

    //Change Status Sale List
    public function changeStatusSaleAccommodation(Request $request)
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

        //Get Sale With Accommodation
        $filter['sale_id'] = $saleID;
        $saleAccommodationData = Transaction::listAccommodationRoom($filter)->first();

        //Check validation status
        if ($oldStatusInDB == TransactionStatus::getPending()) {
            //Only Status Pending
            if ($statusRequest != TransactionStatus::getPendingPayment() &&
                $statusRequest != TransactionStatus::getCancelled() &&
                $statusRequest != TransactionStatus::getRejected()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else if ($oldStatusInDB == TransactionStatus::getPendingPayment()) {
            if ($statusRequest != TransactionStatus::getCancelled() &&
                $statusRequest != TransactionStatus::getRejected()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else if ($oldStatusInDB == TransactionStatus::getAuditingPayment()) {
            if ($statusRequest != TransactionStatus::getApproved() &&
                $statusRequest != TransactionStatus::getRejectPayment()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        //Change to current status
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

            //Set accommodation status by request change status
            if($transaction->{Transaction::STATUS} == TransactionStatus::getApproved()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAccommodationBookingApprove();

                //Transaction Fee and Amount
                $transactionFee = Business::getAppFeeByBusinessID($transaction->{Transaction::BUSINESS_ID});
                $transactionFeeAmount = StringHelper::getTransactionFeeAmount($transaction->{Transaction::SELL_AMOUNT}, $transactionFee);

                // Set Transaction Fee
                $transaction->{Transaction::TRANSACTION_FEE} = $transactionFee;
                $transaction->{Transaction::TRANSACTION_FEE_AMOUNT} = $transactionFeeAmount;
                $transaction->save();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejectPayment()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAccommodationRejectPayment();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejected()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAccommodationReject();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getCancelled()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAccommodationCancel();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getPendingPayment()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAccommodationBookingPayment();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getAuditingPayment()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getAccommodationAuditingPayment();
            }

            //Send notification
            $sendResponse = Notification::accommodationNotification(
                $contactNotiType,
                $contactIDForNotification,
                $transaction->{Transaction::ID},
                $saleAccommodationData
            );
            info('Mobile Notification Change Status Sale Accommodation: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    // Add Payment for Accommodation
    public function addPaymentAccommodation(Request $request)
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
        $saleAccommodationData = Transaction::listAccommodationRoom($filter)->first();

        // Check Status for add payment
        if ($statusInDB == TransactionStatus::getPendingPayment()) {
            $transactionPayment = Transaction::find($saleID);

            // Add More Data into Transaction
            $transactionPayment->bank_account_id = $request->input('bank_account_id');
            $transactionPayment->transaction_date = $request->input('transaction_date');
            $transactionPayment->total_amount = $request->input('total_amount');
            $transactionPayment->updated_at = Carbon::now();
            $transactionPayment->save();
            // Save Data
            if($transactionPayment->save()) {
                // Upload Transaction Image
                $image = StringHelper::uploadImage($request->input('image'), ImagePath::accommodationTransaction);
                $transactionPayment->{Transaction::IMAGE} = $image;
                $transactionPayment->save();
            }

            // Change status
            $transactionPayment->{Transaction::STATUS} = TransactionStatus::getAuditingPayment();
            $transactionPayment->save();

            //Send notification
            $sendResponse = Notification::accommodationNotification(
                ContactNotificationType::getAccommodationAuditingPayment(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $saleAccommodationData
            );
            info('Mobile Notification Change Status Sale Accommodation: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

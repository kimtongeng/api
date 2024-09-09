<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\AdminNotificationType;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\IsBusinessOwner;
use App\Enums\Types\TransactionFeeStatus;
use App\Enums\Types\TransactionStatus;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\TransactionPaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionFeeAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Transaction Fee List
     */
    public function getTransactionFeeList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
            'filter.business_owner_id' => 'required|exists:contact,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sortBy = "indebted_date";
        $filter['is_admin_request'] = true;
        $filter['is_mobile_request'] = true;

        $lists = Transaction::listTransactionFeeCommon($filter, $sortBy)->paginate($tableSize);

        $count_data = $this->getCountDataTransactionFee($filter);

        return response()->json([
            'pagination' => [
                'total' => $lists->total(),
                'per_page' => (int)$lists->perPage(),
                'current_page' => (int)$lists->currentPage(),
                'last_page' => (int)$lists->lastPage(),
                'from' => (int)$lists->firstItem(),
                'to' => (int)$lists->lastItem()
            ],
            'list' => $lists->items(),
            'count_data' => $count_data,
            'success' => 1,
            'message' => 'Your action has been completed successfully.'
        ], 200);
    }

    /**
     * Get CountData Transaction Fee
     */
    private function getCountDataTransactionFee($filter = [])
    {
        $filter['status'] = TransactionFeeStatus::getBusinessNotYetPay();
        $dataOutstanding = Transaction::listTransactionFeeCommon($filter)
        ->get();
        $totalOutstandingAmount = 0;
        foreach ($dataOutstanding as $obj) {
            $totalOutstandingAmount += floatval($obj['total_amount']);
        }

        $filter['status'] = TransactionFeeStatus::getBusinessPaid();
        $dataPaid = Transaction::listTransactionFeeCommon($filter)->get();
        $totalPaidAmount = 0;
        foreach ($dataPaid as $obj) {
            $totalPaidAmount += floatval($obj['total_amount']);
        }

        $response = [
            'outstanding_count' => count($dataOutstanding),
            'paid_count' => count($dataPaid),
            'total_outstanding_amount' => number_format(floatval($totalOutstandingAmount), 2),
            'total_paid_amount' => number_format(floatval($totalPaidAmount), 2),
            'total_all_amount' => number_format(floatval($totalOutstandingAmount) + floatval($totalPaidAmount), 2),
        ];

        return $response;
    }

    /**
     * Return Payment Transaction Fee
     */
    public function repaymentTransactionFee(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'business_type_id' => 'required|exists:business_type,id',
            'bank_account_id' => 'required|exists:bank_account,id',
            'total_payment' => 'required',
            'transaction_date' => 'required',
            'image' => 'required',
        ]);

        DB::beginTransaction();

        $request->merge([TransactionPayment::CONTACT_ID => $request->input('current_user_id')]);

        $transaction_payment = new TransactionPayment();
        $transaction_payment->setData($request);

        if ($transaction_payment->save()) {        //Upload Transaction Image
            $image = StringHelper::uploadImage($request->input(TransactionPayment::IMAGE), ImagePath::transactionFeeSlipImagePath);
            $transaction_payment->{TransactionPayment::IMAGE} = $image;
            $transaction_payment->save();

            //Set Transaction Payment Detail
            $filter = [
                'business_type_id' => $transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID},
                'business_owner_id' => $transaction_payment->{TransactionPayment::CONTACT_ID},
                'status' => TransactionFeeStatus::getBusinessNotYetPay(),
                'group_by_month' => false,
            ];
            $transactionFeeList = Transaction::listTransactionFeeCommon($filter)->get();
            foreach ($transactionFeeList as $obj) {
                $data = [
                    TransactionPaymentDetail::TRANSACTION_PAYMENT_ID => $transaction_payment->{TransactionPayment::ID},
                    TransactionPaymentDetail::TRANSACTION_ID => $obj->{Transaction::ID},
                ];
                $transaction_payment_detail = new TransactionPaymentDetail();
                $transaction_payment_detail->setData($data);

                if ($transaction_payment_detail->save()) {
                    //Set Status Transaction Fee
                    $transaction = Transaction::find($obj->{Transaction::ID});
                    $transaction->{Transaction::TRANSACTION_FEE_STATUS} = TransactionFeeStatus::getBusinessPaid();
                    $transaction->save();
                }
            }

            //Enable Contact Business
            $contact = Contact::find($transaction_payment->{TransactionPayment::CONTACT_ID});
            if ($transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getProperty()) {
                $contact->{Contact::IS_PROPERTY_OWNER} = IsBusinessOwner::getYes();
                $contact->{Contact::IS_AGENCY} = IsBusinessOwner::getYes();
                $contact->save();
            } else if ($transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getAccommodation()) {
                $contact->{Contact::IS_HOTEL_OWNER} = IsBusinessOwner::getYes();
                $contact->save();
            } else if ($transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getAttraction()) {
                $contact->{Contact::IS_ATTRACTION_OWNER} = IsBusinessOwner::getYes();
                $contact->save();
            } else if ($transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getShopRetail() ||
                $transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getShopWholesale() ||
                $transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getRestaurant() ||
                $transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getShopLocalProduct() ||
                $transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getService() ||
                $transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getModernCommunity()
            ) {
                $contact->{Contact::IS_SELLER} = IsBusinessOwner::getYes();
                $contact->save();
            } else if ($transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getMassage()) {
                $contact->{Contact::IS_MASSAGE_OWNER} = IsBusinessOwner::getYes();
                $contact->save();
            } else if ($transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID} == BusinessTypeEnum::getKtv()) {
                $contact->{Contact::IS_KTV_OWNER} = IsBusinessOwner::getYes();
                $contact->save();
            }

            //Send Notification To Admin
            $sendResponse = Notification::payTransactionFee(
                AdminNotificationType::getOwnerPayTransactionFee(),
                $contact,
                $transaction_payment->{TransactionPayment::BUSINESS_TYPE_ID}
            );
            info('Admin Notification Owner Paid Transaction Fee: '. $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Get Transaction Fee Payment History
     */
    public function getTransactionFeePaymentHistory(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_type_id' => 'required|exists:business_type,id',
            'filter.business_owner_id' => 'required|exists:contact,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');

        $lists = TransactionPayment::lists($filter)->paginate($tableSize);

        $count_data = $this->getCountDataTransactionFeePaymentHistory($filter);

        return response()->json([
            'pagination' => [
                'total' => $lists->total(),
                'per_page' => (int)$lists->perPage(),
                'current_page' => (int)$lists->currentPage(),
                'last_page' => (int)$lists->lastPage(),
                'from' => (int)$lists->firstItem(),
                'to' => (int)$lists->lastItem()
            ],
            'list' => $lists->items(),
            'count_data' => $count_data,
            'success' => 1,
            'message' => 'Your action has been completed successfully.'
        ], 200);
    }

    /**
     * Get CountData Transaction Fee PaymentHistory
     */
    private function getCountDataTransactionFeePaymentHistory($filter = [])
    {
        $data = TransactionPayment::lists($filter)->get();
        $totalAmount = 0;
        foreach ($data as $obj) {
            $totalAmount += floatval($obj['total_payment']);
        }

        $response = [
            'total_amount' => number_format(floatval($totalAmount), 2),
        ];

        return $response;
    }

    /**
     * Get Count Transaction Fee By Business
     */
    public function getCountTransactionFeeByBusiness(Request $request){
        $this->validate($request, [
            'business_owner_id' => 'required|exists:contact,id'
        ]);

        //Property
        $filter['business_owner_id'] = $request->input('business_owner_id');
        $filter['status'] = TransactionFeeStatus::getBusinessNotYetPay();
        $filter['business_type_id'] = BusinessTypeEnum::getProperty();
        $totalProperty = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Accommodation
        $filter['business_type_id'] = BusinessTypeEnum::getAccommodation();
        $totalAccommodation = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Delivery
        $filter['business_type_id'] = BusinessTypeEnum::getDelivery();
        $totalDelivery = count(Transaction::listTransactionFeeCommon($filter)->get());

        //ShopRetail
        $filter['business_type_id'] = BusinessTypeEnum::getShopRetail();
        $totalShopRetail = count(Transaction::listTransactionFeeCommon($filter)->get());

        //ShopWholesale
        $filter['business_type_id'] = BusinessTypeEnum::getShopWholesale();
        $totalShopWholesale = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Restaurant
        $filter['business_type_id'] = BusinessTypeEnum::getRestaurant();
        $totalRestaurant = count(Transaction::listTransactionFeeCommon($filter)->get());

        //ShopLocalProduct
        $filter['business_type_id'] = BusinessTypeEnum::getShopLocalProduct();
        $totalShopLocalProduct = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Massage
        $filter['business_type_id'] = BusinessTypeEnum::getMassage();
        $totalMassage = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Attraction
        $filter['business_type_id'] = BusinessTypeEnum::getAttraction();
        $totalAttraction = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Service
        $filter['business_type_id'] = BusinessTypeEnum::getService();
        $totalService = count(Transaction::listTransactionFeeCommon($filter)->get());

        //KTV
        $filter['business_type_id'] = BusinessTypeEnum::getKtv();
        $totalKTV = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Modern Community
        $filter['business_type_id'] = BusinessTypeEnum::getModernCommunity();
        $totalModernCommunity = count(Transaction::listTransactionFeeCommon($filter)->get());

        //Total All
        $totalAll = $totalProperty + $totalAccommodation + $totalDelivery + $totalShopRetail + $totalShopWholesale + $totalRestaurant + $totalShopLocalProduct + $totalMassage + $totalAttraction + $totalService + $totalKTV + $totalModernCommunity;

        $response = [
            'property' => $totalProperty,
            'accommodation' => $totalAccommodation,
            'delivery' => $totalDelivery,
            'shop_retail' => $totalShopRetail,
            'shop_wholesale' => $totalShopWholesale,
            'restaurant' => $totalRestaurant,
            'shop_local_product' => $totalShopLocalProduct,
            'massage' => $totalMassage,
            'attraction' => $totalAttraction,
            'service' => $totalService,
            'ktv' => $totalKTV,
            'modern_community' => $totalModernCommunity,
            'total_all' => $totalAll
        ];
        return $this->responseWithData($response);
    }

    /**
     * Get Property By Transaction Fee Date
     */
    public function getPropertyByTransactionFeeDate(Request $request)
    {
        $this->validate($request, [
            'indebted_date' => 'required',
            'business_owner_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'created_date_by_month_year' => $request->input('indebted_date'),
            'business_owner_id' => $request->input('business_owner_id')
        ];


        $data = Transaction::listPropertyAndAsset($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Shop By Transaction Fee Date
     */
    public function getShopByTransactionFeeDate(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id',
            'indebted_date' => 'required',
            'business_owner_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'business_type_id' => $request->input('business_type_id'),
            'created_date_by_month_year' => $request->input('indebted_date'),
            'business_owner_id' => $request->input('business_owner_id'),
        ];


        $data = Transaction::listShop($filter)
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Accommodation By Transaction Fee Date
     */
    public function getAccommodationByTransactionFeeDate(Request $request)
    {
        $this->validate($request, [
            'indebted_date' => 'required',
            'business_owner_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'created_date_by_month_year' => $request->input('indebted_date'),
            'business_owner_id' => $request->input('business_owner_id')
        ];

        $data = Transaction::listAccommodationRoom($filter)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Massage By Transaction Fee Date
     */
    public function getMassageByTransactionFeeDate(Request $request)
    {
        $this->validate($request, [
            'indebted_date' => 'required',
            'business_owner_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'created_date_by_month_year' => $request->input('indebted_date'),
            'business_owner_id' => $request->input('business_owner_id')
        ];

        $data = Transaction::listMassage($filter)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Attraction By Transaction Fee Date
     */
    public function getAttractionByTransactionFeeDate(Request $request)
    {
        $this->validate($request, [
            'indebted_date' => 'required',
            'business_owner_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'created_date_by_month_year' => $request->input('indebted_date'),
            'business_owner_id' => $request->input('business_owner_id')
        ];

        $data = Transaction::listAttraction($filter)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get KTV By Transaction Fee Date
     */
    public function getKTVByTransactionFeeDate(Request $request)
    {
        $this->validate($request, [
            'indebted_date' => 'required',
            'business_owner_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'created_date_by_month_year' => $request->input('indebted_date'),
            'business_owner_id' => $request->input('business_owner_id')
        ];

        $data = Transaction::listKTV($filter)
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}

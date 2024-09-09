<?php

namespace App\Http\Controllers\Mobile\Modules\Charity;

use App\Enums\Types\CharityTransactionStatus;
use App\Models\Business;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\TransactionStatus;
use App\Enums\Types\ContactNotificationType;

class CharityDonorListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Get Charity Donor List
    public function getCharityDonorList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.organization_owner_id' => !empty($request->input('organization_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.customer_id' => !empty($request->input('customer_id')) ? 'required|exists:contact,id' : 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = empty($request->input('sort')) ? 'newest' : $request->input('sort');

        $data = Transaction::listDonationCharity($filter, $sort)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Charity Donor Detail
    public function getCharityDonorDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.donation_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listDonationCharity($filter)->first();

        return $this->responseWithData($data);
    }

    //Change Status Charity Donor
    public function changeStatusCharityDonor(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'donation_id' => 'required|exists:transaction,id',
            'status' => 'required|numeric|min:2|max:4',
//            'remark' => $request->input('status') == TransactionStatus::getRejected() ? 'required' : 'nullable'
        ]);

        DB::beginTransaction();

        //Current Request Data
        $donationID = $request->input('donation_id');
        $statusRequest = $request->input(Transaction::STATUS);

        //Get Old Data
        $transaction = Transaction::find($donationID);
        $oldStatusInDB = $transaction->{Transaction::STATUS};

        //Check validation status
        if ($oldStatusInDB == TransactionStatus::getPending()) {
            //Only Status Pending
            if ($statusRequest != TransactionStatus::getApproved() &&
                $statusRequest != TransactionStatus::getRejected()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        //Change to current status
        $transaction->{Transaction::STATUS} = $statusRequest;
        if ($transaction->save()) {
            //Send notification
            $contactNotiType = 0;
            $customerID = $transaction->{Transaction::CUSTOMER_ID};
            $notificationData = [
                'organization_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'donation_amount' => $transaction->{Transaction::TOTAL_AMOUNT},
            ];

            if ($transaction->{Transaction::STATUS} == TransactionStatus::getApproved()) {
                $contactNotiType = ContactNotificationType::getCharityDonationApproved();
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejected()) {
                $contactNotiType = ContactNotificationType::getCharityDonationRejected();
            }

            $sendResponse = Notification::charityNotification(
                $contactNotiType,
                $customerID,
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Change Status Charity Donor: ' . $sendResponse);
        }

        DB::commit();

        return $this->responseWithSuccess();
    }

    //Change Active Charity Donor List
    public function changeActiveCharityDonorList(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'donation_id' => 'required|exists:transaction,id',
            'active' => 'required'
        ]);

        DB::beginTransaction();

        $transaction = Transaction::find($request->input('donation_id'));
        $transaction->active = $request->input('active');
        $transaction->save();

        DB::commit();
        return $this->responseWithSuccess();
    }
}

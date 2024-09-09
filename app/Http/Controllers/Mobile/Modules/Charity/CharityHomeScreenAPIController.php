<?php

namespace App\Http\Controllers\Mobile\Modules\Charity;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\PrefixCode;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Enums\Types\AppTypeEnum;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\TransactionStatus;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\CharityTransactionStatus;

class CharityHomeScreenAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Get Charity Organization List
    public function getCharityOrganizationList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [];
        $sort = 'newest';

        $data = Business::listCharityOrganization($filter, $sort)
        ->where('business.status', BusinessStatus::getApproved())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Donation Charity
    public function donationCharity(Request $request)
    {
        $this->validate($request, [
            'organization_id' => 'required|exists:business,id',
            'organization_owner_id' => 'required|exists:contact,id',
            'customer_id' => 'required',
            'bank_account_id' => 'required',
            'transaction_date' => 'required',
            'total_amount' => 'required',
            'image' => 'required',
        ]);

        DB::beginTransaction();

        //Merge Request Some Value
        $request->merge([
            Transaction::APP_TYPE_ID => AppTypeEnum::getCharity(),
            Transaction::BUSINESS_TYPE_ID => BusinessTypeEnum::getCharityOrganization(),
            Transaction::BUSINESS_ID => $request->input('organization_id'),
            Transaction::BUSINESS_OWNER_ID => $request->input('organization_owner_id'),
            Transaction::CODE => PrefixCode::getAutoCodeByBusiness(Transaction::TABLE_NAME, PrefixCode::TRANSACTION, $request->input('organization_id')),
        ]);

        //Set Data
        $transaction = new Transaction();
        $transaction->setData($request);
        $transaction->{Transaction::STATUS} = TransactionStatus::getPending();
        $transaction->{Transaction::ACTIVE} = CharityTransactionStatus::getEnabled();
        $transaction->{Transaction::CREATED_AT} = Carbon::now();

        if ($transaction->save()) {
            //Upload Transaction Image
            $image = StringHelper::uploadImage($request->input(Transaction::IMAGE), ImagePath::charityTransaction);
            $transaction->{Transaction::IMAGE} = $image;
            $transaction->save();

            //Send notification
            $notificationData = [
                'organization_name' => Business::find($transaction->{Transaction::BUSINESS_ID})->name,
                'donation_amount' => $transaction->{Transaction::TOTAL_AMOUNT},
                'customer_name' => Contact::find($transaction->{Transaction::CUSTOMER_ID})->fullname,
            ];
            $sendResponse = Notification::charityNotification(
                ContactNotificationType::getCharityDonation(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Charity Donation: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Get Charity Has Donation List
    public function getCharityHasDonationList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = empty($request->input('sort')) ? 'newest' : $request->input('sort');

        $data = Transaction::listDonationCharity($filter, $sort)
            ->where('transaction.status', TransactionStatus::getApproved())
            ->where('transaction.active', CharityTransactionStatus::getEnabled())
            ->orderBy('transaction.transaction_date', 'desc')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Charity Has Donation Detail
    public function getCharityHasDonationDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.donation_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listDonationCharity($filter)
            ->where('transaction.status', TransactionStatus::getApproved())
            ->first();

        return $this->responseWithData($data);
    }
}

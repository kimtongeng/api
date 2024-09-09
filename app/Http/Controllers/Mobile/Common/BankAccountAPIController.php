<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\BankAccountContactType;
use App\Enums\Types\BankAccountStatus;
use App\Enums\Types\BusinessAgencyTypeEnum;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\BusinessBankAccount;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\BusinessAgencyBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Validation
     *
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:bank_account,id' : 'nullable',
            'contact_id' => 'required|exists:contact,id',
            'bank_id' => 'required|exists:bank,id',
            'account_name' => 'required',
            'account_number' => 'required',
            'account_qr_code' => 'required',
            'link_account' => 'nullable',
            'old_account_qr_code' => !empty($data['id']) ? 'required' : 'nullable',
        ]);
    }


    /**
     * Add Bank Account
     */
    public function addBankAccount(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        $request->merge([BankAccount::CONTACT_TYPE => BankAccountContactType::getContact()]);

        $bank_account = new BankAccount();
        $bank_account->setData($request);

        if ($bank_account->save()) {
            //Set Qr Code
            if (!empty($request->input('account_qr_code'))) {
                $account_qr_code = StringHelper::uploadImage($request->input('account_qr_code'), ImagePath::bankQrCodeImagePath);
                $bank_account->{BankAccount::ACCOUNT_QR_CODE} = $account_qr_code;
                $bank_account->save();
            }

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Update Bank Account
     */
    public function updateBankAccount(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        //Prevent Empty Bank Account ID
        $bank_account = BankAccount::where(BankAccount::ID, $request->input(BankAccount::ID))
            ->where(BankAccount::CONTACT_ID, $request->input(BankAccount::CONTACT_ID))
            ->first();

        $request->merge([BankAccount::CONTACT_TYPE => BankAccountContactType::getContact()]);

        if (!empty($bank_account)) {
            $bank_account->setData($request);

            if ($bank_account->save()) {
                //Set Or Update Qr Code
                $account_qr_code = StringHelper::editImage(
                    $request->input('account_qr_code'),
                    $request->input('old_account_qr_code'),
                    ImagePath::bankQrCodeImagePath
                );
                $bank_account->{BankAccount::ACCOUNT_QR_CODE} = $account_qr_code;
                $bank_account->save();

                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Remove Bank Account
     */
    public function removeBankAccount(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:bank_account,id',
            'contact_id' => 'required|exists:contact,id'
        ]);

        $bank_account = BankAccount::find($request->input(BankAccount::ID));

        if ($bank_account->delete()) {
            //Delete Qr Code
            StringHelper::deleteImage($bank_account->{BankAccount::ACCOUNT_QR_CODE}, ImagePath::bankQrCodeImagePath);

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Get My Bank Account
     */
    public function getMyBankAccount(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.contact_id' => 'required|exists:contact,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $filter[BankAccount::CONTACT_TYPE] = BankAccountContactType::getContact();

        $data = BankAccount::lists($filter)
            ->where('bank_account.status', BankAccountStatus::getEnabled())
            ->orderBy('bank_account.id', 'DESC')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Bank Account By Business
     */
    public function getBankAccountByBusiness(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id,deleted_at,NULL',
        ]);

        $filter = $request->input('filter');
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;

        $data = Bank::lists()
            ->with([
                'bankAccount' => function ($query) use ($businessID) {
                    $query->join('business_bank_account', 'business_bank_account.bank_account_id', 'bank_account.id')
                        ->whereNull('business_bank_account.deleted_at')
                        ->when($businessID, function ($query) use ($businessID) {
                            $query->where('business_bank_account.business_id', $businessID);
                        })
                        ->where('bank_account.contact_type', BankAccountContactType::getContact())
                        ->where('bank_account.status', BankAccountStatus::getEnabled())
                        ->select(
                            'bank_account.*'
                        )
                        ->groupBy('bank_account.id')
                        ->orderBy('bank_account.id', 'DESC')
                        ->get();
                }
            ])
            ->get();

        return $this->responseWithData($data);
    }

    /**
     * Get Bank Account By Agency
     */
    public function getBankAccountByAgency(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.agency_id' => 'required|exists:contact,id',
            'filter.business_agency_type_id' => 'required|exists:business_agency_type,id',
        ]);

        $filter = $request->input('filter');
        $agencyID = isset($filter['agency_id']) ? $filter['agency_id'] : null;
        $businessAgencyTypeID = isset($filter['business_agency_type_id']) ? $filter['business_agency_type_id'] : null;

        $data = Bank::lists()
            ->with([
                'bankAccount' => function ($query) use ($agencyID, $businessAgencyTypeID) {
                    $query->join('business_agency_bank_account', 'business_agency_bank_account.bank_account_id', 'bank_account.id')
                        ->when($agencyID, function ($query) use ($agencyID) {
                            $query->where('bank_account.contact_id', $agencyID);
                        })
                        ->when($businessAgencyTypeID, function ($query) use ($businessAgencyTypeID) {
                            $query->where('business_agency_bank_account.business_agency_type_id', $businessAgencyTypeID);
                        })
                        ->where('bank_account.contact_type', BankAccountContactType::getContact())
                        ->where('bank_account.status', BankAccountStatus::getEnabled())
                        ->select(
                            'bank_account.*'
                        )
                        ->groupBy('bank_account.id')
                        ->orderBy('bank_account.id', 'DESC')
                        ->get();
                }
            ])
            ->get();

        return $this->responseWithData($data);
    }

    /**
     * Get Bank Account Admin
     */
    public function getBankAccountAdmin(Request $request)
    {
        $data = Bank::lists()
            ->with([
                'bankAccount' => function ($query) {
                    $query->where('bank_account.contact_type', BankAccountContactType::getAdmin())
                        ->where('bank_account.status', BankAccountStatus::getEnabled())
                        ->select(
                            'bank_account.*'
                        )
                        ->orderBy('bank_account.id', 'DESC')
                        ->get();
                }
            ])
            ->get();

        return $this->responseWithData($data);
    }

    /**
     * Get Bank Account List Property Agency (https://prnt.sc/lXCYgJJ8CmUT)
     */
    public function getBankAccountListPropertyAgency(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
        ]);

        $filter = [
            'contact_id' => $request->input('current_user_id'),
            'business_agency_type_id' => BusinessAgencyTypeEnum::getPropertyAgency()
        ];

        $data = BusinessAgencyBankAccount::lists($filter)
            ->orderBy('business_agency_bank_account.id', 'desc')
            ->get();

        return $this->responseWithData($data);
    }
}

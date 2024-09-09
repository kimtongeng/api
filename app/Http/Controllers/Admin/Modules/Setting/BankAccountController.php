<?php

namespace App\Http\Controllers\Admin\Modules\Setting;

use App\Enums\Types\BankAccountContactType;
use App\Enums\Types\BankAccountStatus;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Permission;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    const MODULE_KEY = 'bank_account';

    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = $this->getList(
                $request->input('table_size'),
                $request->input('filter'),
                $request->input('sort_by'),
                $request->input('sort_type')
            );
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    private function getList($tableSize, $filter , $sortBy = '' , $sortType = '')
    {
        $tableSize = empty($tableSize) ? 10 : $tableSize;

        $filter['contact_type'] = BankAccountContactType::getAdmin();
        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = BankAccount::lists($filter , $sortBy , $sortType)
            ->orderBy('bank_account.id', 'DESC')
            ->addSelect(
                DB::raw("
                    CASE WHEN bank_account.status = '".BankAccountStatus::getEnabled()."'
                    THEN 'true'
                    ELSE 'false'
                    END 'status'
                ")
            )
            ->paginate($tableSize);
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            'data' => $data->items(),
        ];
        return $response;
    }

    public function getBankList(Request $request)
    {
        $data = Bank::lists()->get();

        return $this->responseWithData($data);
    }

    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            //Merge Some Value
            $request->merge([
                BankAccount::CONTACT_TYPE => BankAccountContactType::getAdmin(),
                BankAccount::CONTACT_ID => Auth::guard('admin')->user()->id,
            ]);

            $bank_account = new BankAccount();
            $bank_account->setData($request);

            if ($bank_account->save()) {
                //Set Qr Code
                if (!empty($request->input('account_qr_code'))) {
                    $account_qr_code = StringHelper::uploadImage($request->input('account_qr_code'), ImagePath::bankQrCodeImagePath);
                    $bank_account->{BankAccount::ACCOUNT_QR_CODE} = $account_qr_code;
                    $bank_account->save();
                }

                // Set Log
                $description = 'Id : ' . $bank_account->id . ', Name : ' . $bank_account->account_name;
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();

            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {

            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            //Merge Some Value
            $request->merge([
                BankAccount::CONTACT_TYPE => BankAccountContactType::getAdmin(),
                BankAccount::CONTACT_ID => Auth::guard('admin')->user()->id,
            ]);

            $bank_account = BankAccount::find($request['id']);
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

                //Set Log
                $description = 'Id : ' . $bank_account->id . ', Name : ' . $bank_account->account_name;
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }
            DB::commit();
            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:bank_account,id'
            ]);

            DB::beginTransaction();

            $bank_account = BankAccount::find($request['id']);

            if ($bank_account->delete()) {
                //Delete Qr Code
                StringHelper::deleteImage($bank_account->{BankAccount::ACCOUNT_QR_CODE}, ImagePath::bankQrCodeImagePath);

                //Set Log
                $description = 'Id : ' . $bank_account->id . ', Name : ' . $bank_account->account_name;
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    public function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:bank_account,id' : 'nullable',
            'bank_id' => 'required|exists:bank,id',
            'account_name' => 'required',
            'account_number' => 'required',
            'account_qr_code' => 'required',
            'old_account_qr_code' => !empty($data['id']) ? 'required' : 'nullable',
            'status' => 'required',
        ]);
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:bank_account,id'
        ]);

        DB::beginTransaction();

        $bank_account = BankAccount::find($request->input('id'));
        $bank_account->status = $request->input('status');
        if ($bank_account->save()) {
            $description = ' Id : ' . $bank_account->id . ', Change Status To: ' . $bank_account->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }
}

<?php

namespace App\Http\Controllers\Admin\Modules\Setting;

use App\Enums\Types\GeneralSettingKey;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    const MODULE_KEY = 'general_setting';

    //lists
    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = GeneralSetting::lists();
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    //Update
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            //update default property transaction fee
            if (Permission::authorize(self::MODULE_KEY, 'change_property_transaction_fee')) {
                $property_transaction_fee = GeneralSetting::where(GeneralSetting::KEY, GeneralSettingKey::getPropertyTransactionFee())
                    ->update([
                        GeneralSetting::VALUE => $request->input('property_transaction_fee'),
                        GeneralSetting::UPDATED_AT => Carbon::now()
                    ]);
            } else {
                return $this->responseNoPermission();
            }

            //update default transaction payment deadline
            if (Permission::authorize(self::MODULE_KEY, 'change_transaction_payment_deadline')) {
                $transaction_payment_deadline = GeneralSetting::where(GeneralSetting::KEY, GeneralSettingKey::getTransactionPaymentDeadline())
                    ->update([
                        GeneralSetting::VALUE => $request->input('transaction_payment_deadline'),
                        GeneralSetting::UPDATED_AT => Carbon::now()
                    ]);
            } else {
                return $this->responseNoPermission();
            }


            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }
}

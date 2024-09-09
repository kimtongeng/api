<?php

namespace App\Http\Controllers\Admin\Modules\Business\SocietySecurity;

use Carbon\Carbon;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Enums\Types\GeneralSettingKey;

class SecurityCodeController extends Controller
{
    const MODULE_KEY = 'security_code';

    //lists
    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = GeneralSetting::select(
                'setting.id',
                'setting.key',
                'setting.value',
            )
            ->where(GeneralSetting::KEY, GeneralSettingKey::getSecurityCode())
            ->first();
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    //update
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            //update default security code
            if (Permission::authorize(self::MODULE_KEY, 'change_security_code')) {
                $security_code = GeneralSetting::where(GeneralSetting::KEY, GeneralSettingKey::getSecurityCode())
                    ->update([
                        GeneralSetting::VALUE => $request->input('code'),
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

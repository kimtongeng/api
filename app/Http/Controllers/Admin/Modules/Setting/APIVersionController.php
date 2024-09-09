<?php

namespace App\Http\Controllers\Admin\Modules\Setting;

use Carbon\Carbon;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\GeneralSettingKey;

class APIVersionController extends Controller
{
    const MODULE_KEY = 'api_version';

    //lists
    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = GeneralSetting::select(
                'setting.id',
                'setting.key',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(setting.value, '$.version')) as version"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(setting.value, '$.min_version')) as min_version")
            )
            ->where(GeneralSetting::KEY, GeneralSettingKey::getAPIVersion())
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
            if (Permission::authorize(self::MODULE_KEY, 'change_api_version')) {
                $api_version = GeneralSetting::where(GeneralSetting::KEY, GeneralSettingKey::getAPIVersion())
                    ->update([
                        GeneralSetting::VALUE => $request->input('value'),
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

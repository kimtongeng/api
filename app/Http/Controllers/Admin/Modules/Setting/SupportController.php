<?php

namespace App\Http\Controllers\Admin\Modules\Setting;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Support;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    const MODULE_KEY = 'support';

    //support lists
    public function get()
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {

            $data = $this->getList();

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    //support update
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $this->checkValidation($request);

            DB::beginTransaction();

            foreach ($request->supportItem as $Item) {

                $support = Support::find($Item['id']);

                $support_data = [
                    Support::SUPPORT_TYPE => $Item['support_type'],
                    Support::SUPPORT_VALUE => $Item['support_value'],
                ];

                $support->setData($support_data);

                if ($support->save()) {

                    // Set Log
                    $description = 'Id : ' . $Item['id'] . ', Support Type : ' . $Item['support_type'] . ', Support Value : ' . $Item['support_value'];
                    UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
                }
            }
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), 'Updated Id : ' . $request['id']);

            DB::commit();
            $data = $this->getList();
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    //check validation function
    public function checkValidation($data)
    {
        $this->validate($data, []);
    }

    //get all data function
    private function getList()
    {
        $response = Support::lists()->get();

        return $response;
    }
}

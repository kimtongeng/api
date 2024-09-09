<?php

namespace App\Http\Controllers\Admin\Modules\Business;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AppFeeController extends Controller
{
    public function updateAppFee(Request $request)
    {
        $update = DB::table('business')
        ->where('id', $request->input('id'))
        ->where('business_type_id', $request->input('business_type_id'))
        ->update([
            'app_fee' => $request->input('app_fee'),
            'updated_at' => Carbon::now()
        ]);

        if ($update) {
            $response = ['app_fee' => $request->input('app_fee')];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

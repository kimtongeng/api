<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicLinkBusinessAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Set Dynamic Link Business
     *
     */
    public function setDynamicLinkBusiness(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id',
            'dynamic_link' => 'required'
        ]);

        $business = Business::find($request->input('business_id'));

        if(!empty($business)){
            $business->{Business::DYNAMIC_LINK} = $request->input('dynamic_link');
            if($business->save()){
                return $this->responseWithSuccess();
            }else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

}

<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessRatingAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function addBusinessRating(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id',
            'business_id' => 'required|exists:business,id',
            'contact_id' => 'required|exists:contact,id',
            'rate' => 'required|numeric|min:1|max:5'
        ]);

        $ratingCount = Rating::where('business_id', $request->input('business_id'))
            ->where('contact_id', $request->input('contact_id'))
            ->get();
        $rateId = Rating::where('rating.contact_id', '=', $request->input('contact_id'))
        ->pluck('id')
        ->first();
        $isRate = count($ratingCount);

        if ($rateId !== null) {
            $rate = Rating::find($rateId);
            if(!empty($rate)) {
                $rate->business_type_id = $request->input('business_type_id');
                $rate->business_id = $request->input('business_id');
                $rate->contact_id = $request->input('contact_id');
                $rate->rate = $request->input('rate');
                $rate->updated_at = Carbon::now();
            }
        } else if($rateId == null) {
            $rate = new Rating();
            $rate->setData($request);
            $rate->created_at = Carbon::now();
        }

        if ($rate->save()) {
            $data = Rating::where('business_id', $request->input('business_id'))
            ->select(DB::raw("AVG(rate) as rate_avg"))
            ->first();

            if (!empty($data)) {
                DB::table(Business::TABLE_NAME)
                    ->where('id', $request->input('business_id'))
                    ->update([
                        'rate_count' => $data->rate_avg,
                        'updated_at' => Carbon::now()
                    ]);

                $response = ['rate_count' => $data->rate_avg];
            }

            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

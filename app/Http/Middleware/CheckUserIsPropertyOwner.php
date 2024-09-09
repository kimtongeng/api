<?php

namespace App\Http\Middleware;

use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\IsBusinessOwner;
use App\Models\Business;
use App\Models\Contact;
use App\Models\Lib;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserIsPropertyOwner
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUser = Auth::guard('mobile')->user();
        $propertyByContact = Business::where(Business::CONTACT_ID, $currentUser->{Contact::ID})
            ->where(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getProperty())
            ->whereNull(Business::DELETED_AT)
            ->get();
        if (count($propertyByContact) == 0) return $next($request);
        if (!empty($currentUser) && $currentUser->{Contact::IS_PROPERTY_OWNER} == IsBusinessOwner::getYes()) {
            return $next($request);
        } else {
            return response()->json(['success' => 0, 'message' => Lib::PER_FAIL], 403);
        }
    }
}

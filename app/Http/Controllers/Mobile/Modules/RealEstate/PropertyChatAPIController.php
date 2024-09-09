<?php

namespace App\Http\Controllers\Mobile\Modules\RealEstate;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BusinessShareContact;

class PropertyChatAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function PropertyListOwnerAndContactShare(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required|exists:business,id'
        ]);

        $business_id = $request->input('business_id');

        //List Owner Of Business
        $listOwner = Business::join('contact', 'contact.id', 'business.contact_id')
            ->where('business.id', $business_id)
            ->select(
                'business.id as business_id',
                'contact.id as contact_id',
                'contact.fullname as contact_name',
                'contact.profile_image as contact_image',
                DB::raw('NULL as action'),
                DB::raw("1 as type"),
            );

        //List Contact Have Permission Of Business
        $listContact = BusinessShareContact::join('contact', 'contact.id', 'business_share_contact.contact_id')
            ->join('business_contact_permission', 'business_share_contact.id', 'business_contact_permission.business_share_contact_id')
            ->join('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
            ->where('business_permission.action', 'chat_with_customer_property')
            ->where('business_share_contact.business_id', $business_id)
            ->select(
                'business_share_contact.business_id',
                'contact.id as contact_id',
                'contact.fullname as contact_name',
                'contact.profile_image as contact_image',
                'business_permission.action',
                DB::raw("0 as type"),
            );


        //Merge List Owner & List Contact Share
        $data = $listOwner->union($listContact)->get();

        return $this->responseWithData($data);
    }
}

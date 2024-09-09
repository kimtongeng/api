<?php


namespace App\Http\Controllers\Mobile\Common;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use App\Enums\Types\ContactStatus;
use App\Models\BusinessPermission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BusinessShareContact;
use Illuminate\Support\Facades\Auth;
use App\Enums\Types\ContactHasPermission;
use App\Models\BusinessContactPermission;
use App\Enums\Types\ContactNotificationType;
use Illuminate\Pagination\LengthAwarePaginator;

class ShareBusinessAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Contact List
     * https://prnt.sc/HhsENat4BIOA
     */
    public function getContactList(Request $request)
    {
        $tableSize = !empty($request->input('table_size')) ? $request->input('table_size') : 10;
        $search = !empty($request->input('search')) ? $request->input('search') : null;
        $data = Contact::select(
            'id as contact_id',
            'fullname as contact_name',
            'code as contact_code',
            'profile_image as contact_profile_image'
        )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('fullname', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%');
                });
            })
            ->where('status', ContactStatus::getActivated())
            ->where('id', '!=', Auth::guard('mobile')->user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Business Permission By Business Type
     * https://prnt.sc/bYn6s6As0WHI
     */
    public function getBusinessPermissionByBusinessType(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id'
        ]);

        $filter = ['business_type_id' => $request->input('business_type_id')];
        $data = BusinessPermission::lists($filter)->get();

        return $this->responseWithData($data);
    }

    /**
     * Get Business Contact Permission
     * https://prnt.sc/DKHm9BOqGT2a
     */
    public function getBusinessContactPermission(Request $request)
    {
        $this->validate($request, [
            'contact_id' => !empty($request['contact_id']) ? 'required|exists:contact,id' : 'nullable',
            'business_id' => !empty($request['business_id']) ? 'required|exists:business,id' : 'nullable'
        ]);

        $tableSize = !empty($request->input('table_size')) ? $request->input('table_size') : 10;
        if (!empty($request->input('contact_id')) || !empty($request->input('business_id'))) {
            $filter = [
                'contact_id' => $request->input('contact_id'),
                'business_id' => $request->input('business_id')
            ];
            $data = BusinessShareContact::lists($filter)->paginate($tableSize);

            return $this->responseWithPagination($data);
        } else {
            $response = [
                'pagination' => [
                    'total' => 0,
                    'per_page' => intval($tableSize),
                    'current_page' => intval($request->page),
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0
                ],
                'data' => []
            ];

            return $this->responseWithData($response);
        }
    }

    /**
     * Share business to contact
     * https://prnt.sc/hQFjWfE8FEwE
     */
    public function shareBusinessToContact(Request $request)
    {
        $this->validate($request, [
            'id' => !empty($request['id']) ? 'required|exists:business_share_contact,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'contact_id' => 'required|exists:contact,id',
            'business_permission_list' => 'required',
            'business_permission_list.*.permission_id' => 'required|exists:business_permission,id',
        ]);

        DB::beginTransaction();

        $contactNotiType = '';

        if (empty($request->input('id'))) {
            $business_share_contact = new BusinessShareContact();
            $business_share_contact->created_at = Carbon::now();
            $contactNotiType = ContactNotificationType::getShareBusinessPermission();
        } else {
            $business_share_contact = BusinessShareContact::find($request->input('id'));
            $business_share_contact->updated_at = Carbon::now();
            $contactNotiType = ContactNotificationType::getUpdateBusinessPermission();
        }

        $business_share_contact->setData($request);
        if ($business_share_contact->save()) {
            //Delete all business contact permission
            BusinessContactPermission::where('business_share_contact_id', $business_share_contact->id)->delete();

            if (!empty($request->input('business_permission_list'))) {
                foreach ($request->input('business_permission_list') as $obj) {
                    $data = [
                        BusinessContactPermission::BUSINESS_SHARE_CONTACT_ID => $business_share_contact->id,
                        BusinessContactPermission::BUSINESS_PERMISSION_ID => $obj['permission_id']
                    ];
                    $business_contact_permission = new BusinessContactPermission();
                    $business_contact_permission->setData($data);
                    $business_contact_permission->save();
                }
            }

            //Send Notification Data
            $notificationData = [
                'business_type_id' => Business::find($business_share_contact->{BusinessShareContact::BUSINESS_ID})->business_type_id,
                'business_image' => $this->responseImagePath(Business::find($business_share_contact->{BusinessShareContact::BUSINESS_ID})->business_type_id) . Business::find($business_share_contact->{BusinessShareContact::BUSINESS_ID})->image,
            ];

            $sendResponse = Notification::sharePermissionBusinessNotification(
                $contactNotiType,
                $business_share_contact->{BusinessShareContact::CONTACT_ID},
                $business_share_contact->{BusinessShareContact::BUSINESS_ID},
                $notificationData
            );
            info('Mobile Notification Share Permission Business: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Delete Share business to contact
     */
    public function deleteShareBusinessToContact(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:business_share_contact,id',
        ]);

        DB::beginTransaction();

        $business_share_contact = BusinessShareContact::find($request->input('id'));
        if ($business_share_contact->delete()) {
            //Delete all business contact permission
            BusinessContactPermission::where('business_share_contact_id', $business_share_contact->id)->delete();

            //Send Notification Data
            $notificationData = [
                'business_type_id' => Business::find($business_share_contact->{BusinessShareContact::BUSINESS_ID})->business_type_id,
                'business_image' => $this->responseImagePath(Business::find($business_share_contact->{BusinessShareContact::BUSINESS_ID})->business_type_id) . Business::find($business_share_contact->{BusinessShareContact::BUSINESS_ID})->image,
            ];

            $sendResponse = Notification::sharePermissionBusinessNotification(
                ContactNotificationType::getDeleteBusinessPermission(),
                $business_share_contact->{BusinessShareContact::CONTACT_ID},
                $business_share_contact->{BusinessShareContact::BUSINESS_ID},
                $notificationData
            );
            info('Mobile Notification Share Permission Business: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Check Contact Has Permission
     */
    public function checkContactHasPermission(Request $request)
    {
        $this->validate($request, [
            'contact_id' => 'required|exists:contact,id',
            'business_id' => !empty($request['business_id']) ? 'required|exists:business,id' : 'nullable',
            'action' => 'required|exists:business_permission,action'
        ]);

        $hasPermission = Contact::checkHasShareBusinessPermission(
            $request->input('contact_id'),
            $request->input('business_id'),
            $request->input('action')
        );
        return $this->responseWithData($hasPermission);
    }

}

<?php

namespace App\Http\Controllers\Mobile\Modules\Massage\Profile;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\BusinessStaff;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BusinessStaffWorkDays;
use App\Enums\Types\BusinessStaffStatus;
use App\Enums\Types\ContactNotificationType;

class MassageTherapistCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:business_staff,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'contact_id' => 'required|exists:contact,id',
            'code' => 'required',
            'business_staff_work_days' => 'required',
            'business_staff_work_days.*.day' => 'required',
            //delete_business_staff_work_days
            'deleted_business_work_days.*.id' => !empty($data['id']) && !empty($data['deleted_business_work_days'])  ? 'required|exists:business_staff_workdays,id' : 'nullable',
            'work_start_time' => 'required',
            'work_end_time' => 'required',
            'status' => 'required',
        ]);
    }

    /**
     * Add Massage Therapist
     *
     */
    public function addMassageTherapist(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $massager = new BusinessStaff();
        //Set Data
        $massager->setData($request);
        // $massager->{BusinessStaff::STATUS} = BusinessStaffStatus::getPending();
        $massager->created_at = Carbon::now();

        //Save Data
        if ($massager->save()) {
            //Set Business Staff Work Days
            if(!empty($request->input('business_staff_work_days'))) {
                foreach ($request->input('business_staff_work_days') as $obj) {
                    $business_staff_work_days_data = [
                        BusinessStaffWorkDays::BUSINESS_ID => $massager->{BusinessStaff::BUSINESS_ID},
                        BusinessStaffWorkDays::CONTACT_ID => $massager->{BusinessStaff::CONTACT_ID},
                        BusinessStaffWorkDays::DAY => $obj['day'],
                        BusinessStaffWorkDays::CREATED_AT => Carbon::now()
                    ];
                    $business_staff_work_days = new BusinessStaffWorkDays();
                    $business_staff_work_days->setData($business_staff_work_days_data);
                    $business_staff_work_days->save();
                }
            }

            //Send Notification
            $notificationData = [
                'business_name' => Business::find($massager->{BusinessStaff::BUSINESS_ID})->name,
                'contact_name' => Contact::find($massager->{BusinessStaff::CONTACT_ID})->fullname,
                'business_image' => Business::find($massager->{BusinessStaff::BUSINESS_ID})->image,
            ];
            $sendResponse = Notification::massageTherapistNotification(
                ContactNotificationType::getMassageTherapistAdd(),
                $massager->{BusinessStaff::CONTACT_ID},
                $massager->{BusinessStaff::BUSINESS_ID},
                $notificationData
            );
            info('Mobile Notification Massage Shop: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Massage Therapist
     *
     */
    public function editMassageTherapist(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $massager = BusinessStaff::find($request->input(BusinessStaff::ID));

        if (!empty($massager)) {
            //SetData
            $massager->setData($request);
            $massager->updated_at = Carbon::now();

            //Save Data
            if ($massager->save()) {
                //Insert Or Update Business Staff Work Days
                if(!empty($request->input('business_staff_work_days'))) {
                    foreach ($request->input('business_staff_work_days') as $obj) {
                        $business_staff_work_days_data = [
                            BusinessStaffWorkDays::BUSINESS_ID => $massager->{BusinessStaff::BUSINESS_ID},
                            BusinessStaffWorkDays::CONTACT_ID => $massager->{BusinessStaff::CONTACT_ID},
                            BusinessStaffWorkDays::DAY => $obj['day'],
                            BusinessStaffWorkDays::CREATED_AT => Carbon::now()
                        ];
                        if(empty($obj[BusinessStaffWorkDays::ID])) {
                            $business_staff_work_days = new BusinessStaffWorkDays();
                        } else {
                            $business_staff_work_days = BusinessStaffWorkDays::find($obj[BusinessStaffWorkDays::ID]);
                        }
                        $business_staff_work_days->setData($business_staff_work_days_data);
                        $business_staff_work_days->save();
                    }
                }

                //Delete Business Staff Work Days
                if(!empty($request->input('deleted_business_work_days'))) {
                    foreach($request->input('deleted_business_work_days') as $obj) {
                        if(!empty($obj[BusinessStaffWorkDays::ID])) {
                            BusinessStaffWorkDays::find($obj[BusinessStaffWorkDays::ID])->delete();
                        }
                    }
                }

                //Send Notification
                $notificationData = [
                    'business_name' => Business::find($massager->{BusinessStaff::BUSINESS_ID})->name,
                    'contact_name' => Contact::find($massager->{BusinessStaff::CONTACT_ID})->fullname,
                    'business_image' => Business::find($massager->{BusinessStaff::BUSINESS_ID})->image,
                ];
                $sendResponse = Notification::massageTherapistNotification(
                    ContactNotificationType::getMassageTherapistUpdate(),
                    $massager->{BusinessStaff::CONTACT_ID},
                    $massager->{BusinessStaff::BUSINESS_ID},
                    $notificationData
                );
                info('Mobile Notification Massage Shop: ' . $sendResponse);

                DB::commit();

                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Delete Massage Therapist
     *
     */
    public function deleteMassageTherapist(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:business_staff,id',
        ]);

        DB::beginTransaction();

        $massager = BusinessStaff::find($request->input(BusinessStaff::ID));

        if($massager->delete()) {
            //Delete Business Staff Work Days
            BusinessStaffWorkDays::Where(BusinessStaffWorkDays::CONTACT_ID, $massager->{BusinessStaff::CONTACT_ID})
            ->delete();
        }

        DB::commit();

        return $this->responseWithSuccess();
    }

    /**
     * Get Massage Therapist
     *
     */
    public function getMassageTherapist(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = BusinessStaff::listMassageTherapist($filter, $sort)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Change Status
     *
     */
    public function changeStatusMassageTherapist(Request $request)
    {
        $this->validate($request, [
            'massager_id' => 'required',
            'status' => 'required|numeric',
        ]);

        DB::beginTransaction();

        //Current Request Data
        $massagerID = $request->input('massager_id');
        $statusRequest = $request->input(BusinessStaff::STATUS);

        //Get Old Data
        $massager = BusinessStaff::find($massagerID);
        $oldStatus = $massager->{BusinessStaff::STATUS};

        //Get Massage Therapist
        $filter['massager_id'] = $massagerID;
        $massageTherapistData = BusinessStaff::listMassageTherapist($filter)->first();
        $businessID = BusinessStaff::find($massagerID)->business_id;

        //Check validation Status
        if ($oldStatus == BusinessStaffStatus::getPending()) {
            //Only status Pending
            if ($statusRequest != BusinessStaffStatus::getEnable() &&
                $statusRequest != BusinessStaffStatus::getReject()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        $massager->{BusinessStaff::STATUS} = $statusRequest;
        if($massager->save()) {
            $contactNotiType = 0;
            $contactID = Business::join('business_staff', 'business.id', 'business_staff.business_id')
                ->where('business_staff.business_id', $businessID)
                ->select('business.contact_id')->first();

            $contactIDForNotification = $contactID->contact_id;

            //Set Massage Therapist status by request change status
            if ($massager->{BusinessStaff::STATUS} == BusinessStaffStatus::getEnable()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageTherapistApprove();
            } else if ($massager->{BusinessStaff::STATUS} == BusinessStaffStatus::getReject()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getMassageTherapistReject();
            }

            $sendResponse = Notification::massageTherapistNotification(
                $contactNotiType,
                $contactIDForNotification,
                $massager->{BusinessStaff::BUSINESS_ID},
                $massageTherapistData
            );
            info('Mobile Notification Change Status Massage Therapist: ' . $sendResponse);

            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

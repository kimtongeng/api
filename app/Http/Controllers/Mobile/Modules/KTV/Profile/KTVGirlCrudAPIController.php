<?php

namespace App\Http\Controllers\Mobile\Modules\KTV\Profile;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\BusinessStaff;
use App\Helpers\Utils\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BusinessStaffWorkDays;
use App\Enums\Types\BusinessStaffStatus;
use App\Enums\Types\ContactNotificationType;

class KTVGirlCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Check Validation
    private function checkValidation($data)
    {
        $uniqueCode = false;
        $oldKTVGirl = BusinessStaff::find($data['id']);

        if (!empty($oldKTVGirl)) {
            //When Update
            if ($data['code'] != $oldKTVGirl->code) {
                $uniqueCode = true;
            }
        } else {
            //When Add
            $uniqueCode = true;
        }

        $messages = [
            'code.unique' => 'validation_unique_code'
        ];

        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:business_staff,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'contact_id' => 'required|exists:contact,id',
            'code' => $uniqueCode ? 'required|unique:business_staff,code,NULL,id,business_id,' . $data['business_id'] . ',deleted_at,NULL' : 'required',
            'price' => 'required',
            'business_staff_work_days' => 'required',
            'business_staff_work_days.*.day' => 'required',
            //delete_business_staff_work_days
            'deleted_business_work_days.*.id' => !empty($data['id']) && !empty($data['deleted_business_work_days'])  ? 'required|exists:business_staff_workdays,id' : 'nullable',
            'work_start_time' => 'required',
            'work_end_time' => 'required',
            'status' => 'required',
        ], $messages);
    }

    //Add KTV Girl
    public function addKTVGirl(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $ktv_girl = new BusinessStaff();
        $ktv_girl->setData($request);
        $ktv_girl->created_at = Carbon::now();

        //Save Data
        if($ktv_girl->save()) {
            //Set Business Staff Work Days
            if (!empty($request->input('business_staff_work_days'))) {
                foreach ($request->input('business_staff_work_days') as $obj) {
                    $business_staff_work_days_data = [
                        BusinessStaffWorkDays::BUSINESS_ID => $ktv_girl->{BusinessStaff::BUSINESS_ID},
                        BusinessStaffWorkDays::CONTACT_ID => $ktv_girl->{BusinessStaff::CONTACT_ID},
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
                'business_name' => Business::find($ktv_girl->{BusinessStaff::BUSINESS_ID})->name,
                'business_image' => Business::find($ktv_girl->{BusinessStaff::BUSINESS_ID})->image,
                'contact_name' => Contact::find($ktv_girl->{BusinessStaff::CONTACT_ID})->fullname,
            ];
            $sendResponse = Notification::ktvGirlNotification(
                ContactNotificationType::getKtvGirlAdd(),
                $ktv_girl->{BusinessStaff::CONTACT_ID},
                $ktv_girl->{BusinessStaff::BUSINESS_ID},
                $notificationData
            );
            info('Mobile Notification KTV: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Edit KTV Girl
    public function editKTVGirl(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $ktv_girl = BusinessStaff::find($request->input(BusinessStaff::ID));

        if(!empty($ktv_girl)) {
            //Set Data
            $ktv_girl->setData($request);
            $ktv_girl->updated_at = Carbon::now();

            if($ktv_girl->save()) {
                //Insert Or Update Business Staff Work Days
                if (!empty($request->input('business_staff_work_days'))) {
                    foreach ($request->input('business_staff_work_days') as $obj) {
                        $business_staff_work_days_data = [
                            BusinessStaffWorkDays::BUSINESS_ID => $ktv_girl->{BusinessStaff::BUSINESS_ID},
                            BusinessStaffWorkDays::CONTACT_ID => $ktv_girl->{BusinessStaff::CONTACT_ID},
                            BusinessStaffWorkDays::DAY => $obj['day'],
                            BusinessStaffWorkDays::CREATED_AT => Carbon::now()
                        ];
                        if (empty($obj[BusinessStaffWorkDays::ID])) {
                            $business_staff_work_days = new BusinessStaffWorkDays();
                        } else {
                            $business_staff_work_days = BusinessStaffWorkDays::find($obj[BusinessStaffWorkDays::ID]);
                        }
                        $business_staff_work_days->setData($business_staff_work_days_data);
                        $business_staff_work_days->save();
                    }
                }

                //Delete Business Staff Work Days
                if (!empty($request->input('deleted_business_work_days'))) {
                    foreach ($request->input('deleted_business_work_days') as $obj) {
                        if (!empty($obj[BusinessStaffWorkDays::ID])) {
                            BusinessStaffWorkDays::find($obj[BusinessStaffWorkDays::ID])->delete();
                        }
                    }
                }

                //Send Notification
                $notificationData = [
                    'business_name' => Business::find($ktv_girl->{BusinessStaff::BUSINESS_ID})->name,
                    'business_image' => Business::find($ktv_girl->{BusinessStaff::BUSINESS_ID})->image,
                    'contact_name' => Contact::find($ktv_girl->{BusinessStaff::CONTACT_ID})->fullname,
                ];
                $sendResponse = Notification::ktvGirlNotification(
                    ContactNotificationType::getKtvGirlUpdate(),
                    $ktv_girl->{BusinessStaff::CONTACT_ID},
                    $ktv_girl->{BusinessStaff::BUSINESS_ID},
                    $notificationData
                );
                info('Mobile Notification KTV: ' . $sendResponse);


                DB::commit();

                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    //Delete KTV Girl
    public function deleteKTVGirl(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:business_staff,id',
        ]);

        DB::beginTransaction();

        $ktv_girl = BusinessStaff::find($request->input(BusinessStaff::ID));

        if ($ktv_girl->delete()) {
            //Delete Business Staff Work Days
            BusinessStaffWorkDays::Where(BusinessStaffWorkDays::CONTACT_ID, $ktv_girl->{BusinessStaff::CONTACT_ID})
                ->delete();
        }

        DB::commit();

        return $this->responseWithSuccess();
    }

    //Get KTV Girl
    public function getMyKTVGirl(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = BusinessStaff::listsKTVGirl($filter, $sort)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    // Change Status KTV Girl
    public function changeStatusKTVGirl(Request $request)
    {
        $this->validate($request, [
            'ktv_girl_id' => 'required',
            'status' => 'required|numeric',
        ]);

        DB::beginTransaction();

        // Current Request Data
        $ktvGirlID = $request->input('ktv_girl_id');
        $statusRequest = $request->input(BusinessStaff::STATUS);

        //Get Old Data
        $ktv_girl = BusinessStaff::find($ktvGirlID);
        $oldStatus = $ktv_girl->{BusinessStaff::STATUS};

        //Get KTV Girl Data
        $filter['ktv_girl_id'] = $ktvGirlID;
        $ktvGirlData = BusinessStaff::listsKTVGirl($filter)->first();
        $businessID = BusinessStaff::find($ktvGirlID)->business_id;

        //Check validation Status
        if ($oldStatus == BusinessStaffStatus::getPending()) {
            //Only status Pending
            if (
                $statusRequest != BusinessStaffStatus::getEnable() &&
                $statusRequest != BusinessStaffStatus::getReject()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        $ktv_girl->{BusinessStaff::STATUS} = $statusRequest;
        if ($ktv_girl->save()) {
            $contactNotiType = 0;
            $contactID = Business::join('business_staff', 'business.id', 'business_staff.business_id')
                ->where('business_staff.business_id', $businessID)
                ->select('business.contact_id')->first();

            $contactIDForNotification = $contactID->contact_id;

            //Set Massage Therapist status by request change status
            if ($ktv_girl->{BusinessStaff::STATUS} == BusinessStaffStatus::getEnable()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvGirlApprove();
            } else if ($ktv_girl->{BusinessStaff::STATUS} == BusinessStaffStatus::getReject()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getKtvGirlReject();
            }

            $sendResponse = Notification::ktvGirlNotification(
                $contactNotiType,
                $contactIDForNotification,
                $ktv_girl->{BusinessStaff::BUSINESS_ID},
                $ktvGirlData
            );
            info('Mobile Notification KTV Girl: ' . $sendResponse);

            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}

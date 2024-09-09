<?php

namespace App\Models;

use App\Enums\Types\BusinessTypeEnum;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessStaff extends Model
{
    use SoftDeletes, \Awobaz\Compoships\Compoships;

    const TABLE_NAME = 'business_staff';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const CODE = 'code';
    const PRICE = 'price';
    const WORK_START_TIME = 'work_start_time';
    const WORK_END_TIME = 'work_end_time';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    // RelationShip Table
    public function businessStaffWorkDays()
    {
        return $this->hasMany(BusinessStaffWorkDays::class,
         [BusinessStaffWorkDays::CONTACT_ID,BusinessStaffWorkDays::BUSINESS_ID],
          [BusinessStaff::CONTACT_ID,BusinessStaff::BUSINESS_ID]);
    }


    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::CODE} = $data[self::CODE];
        isset($data[self::PRICE]) && $this->{self::PRICE} = $data[self::PRICE];
        $this->{self::WORK_START_TIME} = $data[self::WORK_START_TIME];
        $this->{self::WORK_END_TIME} = $data[self::WORK_END_TIME];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    public static function listMassageTherapist($filter = [], $sortBy = '')
    {
        //Filter
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $contactID = isset($filter['contact_id']) ? $filter['contact_id'] : null;
        $massagerID = isset($filter['massager_id']) ? $filter['massager_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('business', 'business.id', 'business_staff.business_id')
        ->join('contact', 'contact.id', 'business_staff.contact_id')
        ->select(
            'business_staff.id',
            'business.id as business_id',
            'business.name as business_name',
            'business.image as business_image',
            'contact.id as contact_id',
            'contact.fullname as name',
            'contact.gender',
            'contact.agency_phone as phone',
            'contact.profile_image',
            'business_staff.code',
            'business_staff.work_start_time',
            'business_staff.work_end_time',
            'business_staff.status',
            'business_staff.created_at',
        )
        ->when($businessID , function ($query) use ($businessID) {
            $query->where('business_staff.business_id', $businessID);
        })
        ->when($contactID, function ($query) use ($contactID) {
            $query->where('business_staff.contact_id', $contactID);
        })
        ->when($massagerID, function ($query) use ($massagerID) {
            $query->where('business_staff.id', $massagerID);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('contact.fullname', 'LIKE', '%' . $search . '%')
                ->orWhere('business_staff.code', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($status, function ($query) use ($status) {
            $query->where('business_staff.status', $status);
        })
        ->whereNull('business_staff.deleted_at')
        ->with([
            'businessStaffWorkDays' => function ($query) {
                $query->select(
                    'business_staff_workdays.id',
                    'business_staff_workdays.business_id',
                    'business_staff_workdays.contact_id',
                    'business_staff_workdays.day',
                    'business_staff_workdays.created_at',
                );
            }
        ])
        ->groupBy('business_staff.id');
    }

    // Generate Time Slot
    public function generateTimeSlots($start_time, $end_time , $contact_id , $duration, $contact_massager) {

        $start_time = Carbon::parse($start_time);
        $end_time = Carbon::parse($end_time);
        $duration = CarbonInterval::minutes($duration);

        $current_time = $start_time;
        $time_slots = [];

        while ($current_time->lt($end_time)) {
            $slot_start = $current_time->format('H:i:s');
            $current_time->add($duration);
            $slot_end = $current_time->format('H:i:s');

            $time_slots[] = [
                'contact_id' => $contact_id,
                'start_time' => $slot_start,
                'end_time' => $slot_end,
                'time' => "$slot_start - $slot_end",
                'status' => 1
            ];
        }

        foreach($time_slots as $index => $obj) {
            foreach($contact_massager as $eleIndex => $ele) {
                if (
                    $obj['start_time'] == $ele['start_time'] && $obj['end_time'] == $ele['end_time']
                ) {
                    $time_slots[$index]['status'] = 0;
                    break;
                } else if ($obj['start_time'] <= $ele['start_time'] && $obj['end_time'] >= $ele['end_time']) {
                    $time_slots[$index]['status'] = 0;
                    break;
                }
            }

            // Check if the time slot is smaller than the current time
            if (Carbon::parse($obj['start_time']) < Carbon::now()) {
                $time_slots[$index]['status'] = 0;
            }
        }

        return $time_slots;
    }

    //List KTV Girl
    public static function listsKTVGirl($filter = [], $sortBy = "")
    {
        //Filter
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $contactID = isset($filter['contact_id']) ? $filter['contact_id'] : null;
        $ktvGirlID = isset($filter['ktv_girl_id']) ? $filter['ktv_girl_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('business', 'business.id', 'business_staff.business_id')
            ->join('contact', 'contact.id', 'business_staff.contact_id')
            ->leftjoin('contact_business_info', function ($join) {
                $join->on('contact_business_info.contact_id', '=', 'contact.id')
                    ->where('contact_business_info.business_type_id', '=', BusinessTypeEnum::getKtv());
            })
            ->select(
                'business_staff.id',
                'business.id as business_id',
                'business.name as business_name',
                'business.image as business_image',
                'contact.id as contact_id',
                'contact.fullname as name',
                'contact.gender',
                'contact_business_info.phone',
                'contact_business_info.image',
                'business_staff.code',
                'business_staff.price',
                'business_staff.work_start_time',
                'business_staff.work_end_time',
                'business_staff.status',
                'business_staff.created_at',
            )
            ->where('business.business_type_id', BusinessTypeEnum::getKtv())
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('business_staff.business_id', $businessID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('contact.fullname', 'LIKE', '%' . $search . '%')
                        ->orWhere('business_staff.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($contactID, function ($query) use ($contactID) {
                $query->where('business_staff.contact_id', $contactID);
            })
            ->when($ktvGirlID, function ($query) use ($ktvGirlID) {
                $query->where('business_staff.id', $ktvGirlID);
            })
            ->whereNull('business.deleted_at')
            ->with([
                'businessStaffWorkDays' => function ($query) {
                    $query
                        ->select(
                            'business_staff_workdays.id',
                            'business_staff_workdays.business_id',
                            'business_staff_workdays.contact_id',
                            'business_staff_workdays.day',
                            'business_staff_workdays.created_at',
                        )
                        ->get();
                }
            ])
            ->groupBy('business_staff.id');
    }

}
